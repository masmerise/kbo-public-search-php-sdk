#!/usr/bin/env python3
"""Generate the SDK's code enums from KBO's official code tables.

The tables come from
https://economie.fgov.be/nl/themas/ondernemingen/kruispuntbank-van/diensten-voor-administraties/codetabellen
and are kept next to this script. To refresh: download the new file over the old one, run this script, then
run the test suite. See README.md.

Requires openpyxl (for .xlsx) and xlrd (for .xls).
"""

from __future__ import annotations

import datetime
import pathlib
import re
import sys
import unicodedata
from collections import defaultdict
from dataclasses import dataclass

HERE = pathlib.Path(__file__).resolve().parent
SRC = HERE.parent.parent / "src"


@dataclass(frozen=True)
class Table:
    file: str
    sheet: str
    enum: str  # fully qualified class name below KboPublicSearch\
    summary: str
    code_column: str = "CODE"


TABLES = [
    Table("KBO-codes-legal.xlsx", "Status", "Core\\Type\\Status\\StatusCode", "The status of an enterprise, establishment unit or branch"),
    Table("KBO-codes-identificatie.xlsx", "Denomination", "Core\\Type\\Denomination\\DenominationCode", "The type of a denomination"),
    Table("KBO-codes-contact.xls", "TypeContactData", "Core\\Type\\ContactInformation\\ContactType", "The type of contact information"),
    Table("KBO-codes-quality-aut-activities.xlsx", "ActivityGroup", "Core\\Type\\Activity\\ActivityGroup", "The administration that registered an activity"),
    Table("KBO-codes-strike-out.xls", "StrikeOut", "Core\\Type\\ExOfficioStrikingOff\\ExOfficioStrikingOffCode", "The type of an ex officio striking off"),
    Table("KBO-codes-Technical-Data.xlsx", "TechnicalCreationReason", "Core\\Type\\Address\\TechnicalCreationReasonCode", "Why an address was created for technical reasons"),
    Table("KBO-codes-identificatie.xlsx", "Country", "Core\\Type\\Address\\CountryCode", "A country (NIS code)", code_column="NIS"),
    Table("KBO-codes-legal.xlsx", "JuridicalSituation", "Enterprise\\Type\\JuridicalSituationCode", "A juridical situation"),
    Table("KBO-codes-legal.xlsx", "Event", "Enterprise\\Type\\EventCode", "An event on a juridical situation"),
    Table("KBO-codes-legal.xlsx", "JuridicalForm", "Enterprise\\Type\\JuridicalFormCode", "A juridical form"),
    Table("KBO-codes-legal.xlsx", "LegalFunction", "Enterprise\\Type\\FunctionCode", "A function"),
    Table("KBO-codes-legal.xlsx", "Profession", "Enterprise\\Type\\ProfessionalCompetenceCode", "A professional competence"),
    Table("KBO-codes-legal.xlsx", "LinkedEnterprise", "Enterprise\\Type\\LinkedEnterpriseCode", "The type of a relation between two enterprises"),
    Table("KBO-codes-financial.xls", "Currency", "Enterprise\\Type\\Currency", "The currency of a capital"),
]


def rows(path: pathlib.Path, sheet: str) -> list[list[str]]:
    if path.suffix == ".xlsx":
        import openpyxl

        ws = openpyxl.load_workbook(path, read_only=True, data_only=True)[sheet]
        return [[clean(c) for c in row] for row in ws.iter_rows(values_only=True)]
    import xlrd

    ws = xlrd.open_workbook(str(path)).sheet_by_name(sheet)
    return [[clean(c) for c in ws.row_values(i)] for i in range(ws.nrows)]


def clean(value: object) -> str:
    if value is None:
        return ""
    if isinstance(value, datetime.datetime):
        return value.strftime("%Y-%m-%d")
    if isinstance(value, float) and value.is_integer():
        value = int(value)
    return re.sub(r"\s+", " ", str(value).replace("\xa0", " ")).strip()


def records(table: Table) -> list[dict[str, str]]:
    header: list[str] | None = None
    found = []
    for row in rows(HERE / table.file, table.sheet):
        if header is None:
            if table.code_column in [c.upper() for c in row]:
                header = [c.upper() for c in row]
            continue
        record = dict(zip(header, row))
        if record.get(table.code_column):
            found.append(record)
    if not found:
        sys.exit(f"{table.file} :: {table.sheet}: no codes found under column {table.code_column}")
    return found


def english(record: dict[str, str]) -> str:
    for key in ("ENGELSE OMSCHRIJVING", "ENGELSE BENAMING", "NEDERLANDSE OMSCHRIJVING", "NEDERLANDSE BENAMING"):
        if record.get(key):
            return record[key]
    return ""


def dutch(record: dict[str, str]) -> str:
    return record.get("NEDERLANDSE OMSCHRIJVING") or record.get("NEDERLANDSE BENAMING") or ""


MONTHS = {m: i for i, m in enumerate(["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"], 1)}


def end_date(record: dict[str, str]) -> str | None:
    """The end date as YYYY-MM-DD, or None while the code is still in use."""
    raw = record.get("EINDDATUM", "")
    for pattern, order in ((r"(\d{4})-(\d{2})-(\d{2})", "ymd"), (r"(\d{2})[./](\d{2})[./](\d{4})", "dmy")):
        if m := re.search(pattern, raw):
            y, mo, d = (m.group(1), m.group(2), m.group(3)) if order == "ymd" else (m.group(3), m.group(2), m.group(1))
            return None if y == "9999" else f"{y}-{mo}-{d}"
    if m := re.search(r"(\d{2})([A-Za-z]{3})(\d{4})", raw):
        return None if m.group(3) == "9999" else f"{m.group(3)}-{MONTHS[m.group(2).lower()]:02d}-{m.group(1)}"
    return None


def case_name(text: str) -> str:
    ascii_text = unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode()
    name = "".join(w[:1].upper() + w[1:] for w in re.findall(r"[A-Za-z0-9]+", ascii_text))
    return name if name and not name[0].isdigit() else f"Code{name}"


def generate(table: Table) -> pathlib.Path:
    items = records(table)
    names: dict[str, list[dict[str, str]]] = defaultdict(list)
    for record in items:
        names[case_name(english(record))].append(record)

    namespace, _, enum = f"KboPublicSearch\\{table.enum}".rpartition("\\")
    lines = [
        "<?php declare(strict_types=1);",
        "",
        f"namespace {namespace};",
        "",
        "/**",
        f" * {table.summary} (KBO code table \"{table.sheet}\", docs/codes/{table.file}).",
        " *",
        " * Generated by docs/codes/generate.py: regenerate it instead of editing it by hand.",
        " */",
        f"enum {enum}: string",
        "{",
    ]
    seen = set()
    for record in items:
        code = record[table.code_column]
        if code in seen:
            sys.exit(f"{table.file} :: {table.sheet}: duplicate code {code}")
        seen.add(code)
        name = case_name(english(record))
        if len(names[name]) > 1:
            name = f"{name}{re.sub(r'[^A-Za-z0-9]', '', code)}"
        doc = english(record)
        if dutch(record) and dutch(record) != doc:
            doc += f" ({dutch(record)})"
        if iso := record.get("ISO", "").strip("- "):
            doc += f", ISO {iso}"
        if until := end_date(record):
            doc += f"; no longer assigned since {until}"
        doc = doc.replace("*/", "* /")
        lines.append(f"    /** {doc}. */")
        lines.append(f"    case {name} = '{code}';")
    lines += ["}", ""]

    path = SRC / (table.enum.replace("\\", "/") + ".php")
    path.write_text("\n".join(lines), encoding="utf-8")
    return path


if __name__ == "__main__":
    for t in TABLES:
        print(f"{generate(t).relative_to(SRC.parent)} ({t.sheet})")
