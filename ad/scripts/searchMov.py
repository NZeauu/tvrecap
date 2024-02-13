from tmdbv3api import TMDb

tmdb = TMDb()
tmdb.api_key = '7211e6e46ba968b31e37cd8632cd1205'

tmdb.language = 'fr'

from tmdbv3api import TV, Season, Search, Movie
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

    for s in searchResult:
        runtime = movie.details(s.id).runtime
        cast = movie.credits(s.id)

        cover = s.poster_path

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
            'actors': actors
        })

elif type == 'serie':
    searchResult = search.tv_shows(title, release_year=int(year))

    for s in searchResult:
        result.append({
            'id': s.id,
            'name': s.name,
            'overview': s.overview,
            'poster_path': s.poster_path,
            'first_air_date': s.first_air_date
        })

# print(searchResult)
print(json.dumps(result))
