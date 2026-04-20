#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DUMP_FILE="dump_esport_hub_${TIMESTAMP}.sql"
docker exec esport_db pg_dump -U laravel esport_hub > "$DUMP_FILE"
echo "Dump généré : $DUMP_FILE"
