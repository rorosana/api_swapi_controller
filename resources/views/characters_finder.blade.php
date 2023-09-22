<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & JQUERY-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <title>Character Finder</title>

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        h1 {
            margin: 1em 0;
            font-size: 1.3em;
        }
        @media (max-width: 768px) {
            .col-sm-12 {
                margin-top: 1em;
            }
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <h1 class="text-center">Human Finder</h1>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-8 col-sm-12">
                    <input class="form-control" id="name" aria-describedby="name" placeholder="Search by random string">
                </div>
                <div class="col-md-4 col-sm-12">
                    <button id="search-button" type="button" class="btn btn-outline-primary">SEARCH</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <select id="hair-color-select" class="form-select" aria-label="Hair color selector">
                        <option selected>Select hair color</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-6">
                    <select id="skin-color-select" class="form-select" aria-label="Skin color selector">
                        <option selected>Select skin color</option>
                    </select>
                </div>
                <div class="col-md-4 col-sm-12">
                    <button id="apply-filters-button" type="button" class="btn btn-outline-primary">APPLY FILTERS</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Details</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ url('/') }}">Enunciado</a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <!-- Script -->
    <script type='text/javascript'>
    $(document).ready(function() {
    csrfToken = $('meta[name="csrf-token"]').attr('content');
    $('#search-button').click(function() {
        var selectedHairColor = $('#hair-color-select').val();
        var selectedSkinColor = $('#skin-color-select').val();
        var searchText = $('#name').val();


        $.ajax({
            url: '/search',
            method: 'POST',
            data: {
                _token: csrfToken,
                hair_color: selectedHairColor,
                skin_color: selectedSkinColor,
                searchText: searchText
            },
            success: function(response) {

                var tableBody = $('.table tbody');
                tableBody.empty();

                $.each(response.characters, function(index, character) {
                    var row = '<tr>';
                    row += '<td>' + character.name + '</td>';
                    row += '<td><a href="' + character.url + '">Details</a></td>';
                    row += '</tr>';
                    tableBody.append(row);
                });
            },
            error: function() {
                alert('Error al filtrar los datos');
            }
        });
    });
});

    // fill select options
    $(document).ready(function() {
      $.ajax({
        url: 'https://swapi.dev/api/species/1/',
        method: 'GET',
        success: function (data) {
            var hairColors = data.hair_colors.split(',').map(function(color) {
                return color.trim();
            });

            var hairColorSelect = document.getElementById('hair-color-select');
            for (var i = 0; i < hairColors.length; i++) {
                var option = document.createElement('option');
                option.text = hairColors[i];
                hairColorSelect.appendChild(option);
            }


            var skinColors = data.skin_colors.split(',').map(function(color) {
                return color.trim();
            });


            var skinColorSelect = document.getElementById('skin-color-select');
            for (var i = 0; i < skinColors.length; i++) {
                var option = document.createElement('option');
                option.text = skinColors[i];
                skinColorSelect.appendChild(option);
            }
        },
        error: function () {
            alert('Error al obtener datos de la especie humana');
        }
    });
    //data to filter function
    $(document).ready(function() {
    $('#apply-filters-button').click(function() {
        var selectedHairColor = $('#hair-color-select').val();
        var selectedSkinColor = $('#skin-color-select').val();


        var hairColorCharacterNames = {
            "brown": ["Dormé"],
            "white": ["Dooku"],
            "black": ["Bail Prestor Organa", "Jango Fett"],
        };

        var skinColorCharacterNames = {
            "hispanic": ["Dormé"],
            "caucasian": ["Dooku"],
            "asian": ["Bail Prestor Organa", "Jango Fett"]
        };

        // Filtrar personajes por color de pelo
        var filteredByHairColor = [];
        if (selectedHairColor && hairColorCharacterNames[selectedHairColor]) {
            filteredByHairColor = hairColorCharacterNames[selectedHairColor];
        }


        var filteredCharacters = [];
        if (selectedSkinColor && skinColorCharacterNames[selectedSkinColor]) {
            var charactersToCheck = selectedSkinColor === 'all' ? filteredByHairColor : filteredCharacters;
            filteredCharacters = charactersToCheck.filter(function(character) {
                return skinColorCharacterNames[selectedSkinColor].includes(character);
            });
        } else {
            filteredCharacters = filteredByHairColor;
        }

        // Actualizar la tabla con los resultados filtrados
        var tableBody = $('.table tbody');
        tableBody.empty();

        $.each(filteredCharacters, function(index, characterName) {
            var row = '<tr>';
            row += '<td>' + characterName + '</td>';
            row += '<td><a href="#">Details</a></td>';
            row += '</tr>';
            tableBody.append(row);
        });
    });
});



});
    </script>
</div>
</body>
</html>
