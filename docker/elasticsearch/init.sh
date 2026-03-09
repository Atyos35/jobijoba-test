#!/bin/sh

apk add --no-cache jq > /dev/null 2>&1

ES="http://elasticsearch:9200"
INDEX="job_fr"

echo "Suppression de l'index si existant..."
curl -s -X DELETE "$ES/$INDEX"

echo "\nCréation de l'index avec les settings..."
curl -s -X PUT "$ES/$INDEX" \
  -H "Content-Type: application/json" \
  -d "$(jq '{"settings": .job_fr.settings}' /data/settings.json)"

echo "\nApplication du mapping..."
curl -s -X PUT "$ES/$INDEX/_mapping" \
  -H "Content-Type: application/json" \
  -d "$(jq '.mappings' /data/mapping.json)"

echo "\nConversion et indexation des documents..."
jq -c '. | {"index": {"_id": ._id}}, ._source' /data/jobs.json | \
curl -s -X POST "$ES/$INDEX/_bulk" \
  -H "Content-Type: application/x-ndjson" \
  --data-binary @-

echo "\nTerminé !"
