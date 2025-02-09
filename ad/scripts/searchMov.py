from tmdbv3api import TMDb

tmdb = TMDb()
tmdb.api_key = 'YOUR_API_KEY'

tmdb.language = 'fr'

from tmdbv3api import TV, Search, Movie, Season
import json
import sys

# Get the arguments from the command-line
type = sys.argv[1]
title = sys.argv[2]
year = sys.argv[3]


tv = TV()

search = Search()

result = []
i = 1

if type == 'movie':

    movie = Movie()

    searchResult = search.movies(title, release_year=int(year))

    if len(searchResult['results']) == 0:
        print(json.dumps("No results"))
        sys.exit(1)

    for s in searchResult:
        runtime = movie.details(s.id).runtime
        cast = movie.credits(s.id)

        cover = s.poster_path

        genres = []

        movieGenres = movie.details(s.id).genres
        for g in movieGenres:
            genres.append(g['name'])

        if cover is None:
            cover = '../img/noimg.png'
        else:
            cover = 'image.tmdb.org/t/p/w500' + cover

        n = 0

        actors = []

        for c in cast['cast']:
            if c['known_for_department'] == 'Acting' and n < 3:
                actors.append(c['name'])
            n += 1

        # print(cast)
        result.append({
            'id': s.id,
            'title': s.title,
            'overview': s.overview,
            'poster_path': cover,
            'release_date': s.release_date,
            'duration': runtime,
            'actors': actors,
            'genres': genres
        })

elif type == 'serie':

    series = TV()
    season = Season()

    searchResult = search.tv_shows(title, release_year=int(year))

    if len(searchResult['results']) == 0:
        print(json.dumps("No results"))
        sys.exit(1)

    for s in searchResult:

        numSeasons = 1

        cast = series.credits(s.id)

        if season.details(s.id, numSeasons) is None:
            print("No season found for " + s.name)
            continue

        while season.details(s.id, numSeasons) is not None:
            numSeasons += 1

        cover = s.poster_path

        genres = []

        serieGenres = series.details(s.id).genres
        for g in serieGenres:
            genres.append(g['name'])

        if cover is None:
            cover = '../img/noimg.png'
        else:
            cover = 'image.tmdb.org/t/p/w500' + cover


        n = 0

        actors = []

        for c in cast['cast']:
            if c['known_for_department'] == 'Acting' and n < 3:
                actors.append(c['name'])
            n += 1

        result.append({
            'id': s.id,
            'name': s.name,
            'overview': s.overview,
            'poster_path': cover,
            'first_air_date': s.first_air_date,
            'actors': actors,
            'genres': genres,
            'nb_seasons': numSeasons - 1
        })

print(json.dumps(result))