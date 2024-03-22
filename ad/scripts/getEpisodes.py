from tmdbv3api import TMDb

tmdb = TMDb()
tmdb.api_key = '7211e6e46ba968b31e37cd8632cd1205'

tmdb.language = 'fr'

import sys
import os
from tmdbv3api import TV, Season
import json

# Get the arguments from the command-line
contentID = sys.argv[1]
title = sys.argv[2]

tv = TV()
season = Season()

episodes = {}
episodes['episodes'] = []

n = 1


if season.details(contentID, n) is None:
    print("No season found for " + title)
    sys.exit(1)

while season.details(contentID, n) is not None:

    show_season = season.details(contentID, n)

    for episode in show_season.episodes:

        episodes['episodes'].append({
            'title': title,
            'season': n,
            'episode-name': episode.name,
            'runtime': episode.runtime,
            'number': episode.episode_number
        })

        
    n += 1

# IF YOU NEED TO CHECK THE RESULTS (DEBUGGING)
# DON'T FORGET TO COMMENT THE LINE AFTER THE DEBUGGING PRINT TO AVOID SENDING THE DATA TO THE DATABASE DURING DEBUGGING
# print(json.dumps(episodes))

# Connect to the database
import mysql.connector

conn = mysql.connector.connect(
    host="127.0.0.1",
    user="pma_user",
    password="gecT5HCPQKD63xgF3h8",
    database="tvrecap"
)

cursor = conn.cursor()

# Insert the episodes in the database
for episode in episodes['episodes']:
    cursor.execute("INSERT INTO Episodes (nom, num_ep, duree, saison, serie_id) VALUES (%s, %s, %s, %s, (SELECT id FROM Séries WHERE nom=%s));", (episode['episode-name'], episode['number'], episode['runtime'], episode['season'], episode['title']))

conn.commit()

# Close the connection
conn.close()