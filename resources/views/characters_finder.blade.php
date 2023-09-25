<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS & JQUERY-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!--FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('../../css/styles.css') }}">

    <title>Character Finder</title>
</head>

<body>
    <nav class="navbar">
        <span class="navbar-text">pedagoo</span>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 d-flex align-items-center justify-content-start">
                <a href="{{ url('/') }}" class="custom-link">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="col-md-8 d-flex align-items-center justify-content-center">
                <h1 class="text-center">Human Finder</h1>
            </div>
        </div>
        <div class="card mb-3">
            <div class="row align-items-center mt-4">
                <div class="col">
                    <p class="text-muted text-with-margin mb-0">Refresh the cache to load data from swapi.com</p>
                    <a href="{{ route('refreshCache') }}" class="custom-link ">Refresh Caché</a>
                </div>

            </div>

            <div class="card-body ">
                <form method="POST" action="{{ route('search') }}">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-10 col-sm-12">
                            <div class="input-group">
                                <input class="form-control form-control-icon" id="search-term" name="search_term"
                                    aria-describedby="name" placeholder="Search by random string">
                                <i class="fas fa-search fa-lg"></i>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="custom-button">SEARCH</button>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-5 col-sm-6 mb-3 ">
                        <select id="hair-color-select" class="form-select custom-select-height"
                            aria-label="Hair color selector" name="hair_color">
                            <option selected>Select hair color</option>
                        </select>
                    </div>
                    <div class="col-md-5 col-sm-6">
                        <select id="skin-color-select" class="form-select custom-select-height"
                            aria-label="Skin color selector" name="skin_color">
                            <option selected>Select skin color</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-sm-12 ml-n4">
                        <button id="apply-filters-button" type="button" class="custom-button">APPLY
                            FILTERS</button>
                    </div>
                </div>
            </div>
        </div>



        <div class="card mb-4" id="characters-table">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Comentario Blade --}}
                        @foreach ($characters as $character)
                        <tr>
                            <td>{{ $character->nombre }}</td>
                            <td>
                                <span class="character-details-icon" data-character="{{ json_encode($character) }}"><i
                                        class="fa-solid fa-user"></i></span>
                            </td>
                            {{-- Comentario Blade --}}
                            {{-- <td>
                                {{ dd($character) }}
                            </td>--}}
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <!-- Script SEARCH-->
    <script type='text/javascript'>
        $(document).ready(function () {
    $('#search-form').submit(function (e) {
        e.preventDefault();

        var searchTerm = $('#search-term').val();

        $.ajax({
            type: 'POST',
            url: '{{ route('search') }}',
            data: { '_token': '{{ csrf_token() }}', 'search_term': searchTerm },
            success: function (data) {
                console.log(data);
                var $resultsTable = $('#characters-table tbody');
                var $noResultsMessage = $('#no-results-message');

                $resultsTable.empty();

                if (data.length === 0) {

                    $('#no-results-message').show();
                    $('table').hide();
                } else {

                    $('#no-results-message').hide();
                    $('table').show();
                }



                $.each(data, function (index, character) {
                    var row = '<tr><td>' + character.nombre + '</td><td>' + character.details + '</td></tr>';
                    $resultsTable.append(row);
                });
            },
            error: function (error) {
                console.error(error);
            }
        });
    });
});
    </script>


    <!-- Script FILL SELECT OPTIONS -->
    <script type='text/javascript'>
        $(document).ready(function () {
    $.ajax({
        type: 'GET',
        url: '{{ route('getColors') }}',
        success: function (data) {
            var hairColors = data.hairColors;
            var skinColors = data.skinColors;

            var hairColorSelect = $('#hair-color-select');
            var skinColorSelect = $('#skin-color-select');

            hairColorSelect.empty();
            skinColorSelect.empty();

            hairColorSelect.append($('<option>', {
                value: 'Select hair color',
                text: 'Select hair color'
            }));

            skinColorSelect.append($('<option>', {
                value: 'Select skin color',
                text: 'Select skin color'
            }));

            $.each(hairColors, function (index, color) {
                hairColorSelect.append($('<option>', {
                    value: color,
                    text: color
                }));
            });

            $.each(skinColors, function (index, color) {
                skinColorSelect.append($('<option>', {
                    value: color,
                    text: color
                }));
            });
        },
        error: function (error) {
            console.error(error);
        }
    });

});
    </script>

    <!-- Script APPLY FILTERS -->
    <script type='text/javascript'>
        $(document).ready(function () {
    $('#apply-filters-button').click(function () {
        var hairColor = $('#hair-color-select').val();
        var skinColor = $('#skin-color-select').val();

        $.ajax({
            type: 'POST',
            url: '{{ route('searchWithFilters') }}',
            data: {
                '_token': '{{ csrf_token() }}',
                'search_term': '',
                'hair_color': hairColor,
                'skin_color': skinColor
            },
            success: function (data) {
                console.log(data);
                var $resultsTable = $('#characters-table tbody');

                $resultsTable.empty();

                $.each(data, function (index, character) {
                    var row = '<tr><td>' + character.nombre + '</td><td>' + character.details + '</td></tr>';
                    $resultsTable.append(row);
                });
            },
            error: function (error) {
                console.error(error);
            }
        });
    });
});
    </script>

    <!-- Script POPUP -->
    <script type="text/javascript">
        $(document).ready(function () {
        // Mostrar el popup al hacer clic en el ícono
        $('.character-details-icon').click(function () {
            console.log("Clicked!");
            //var characterData = JSON.parse($(this).data('character'));
            var characterData = $(this).data('character');

            console.log($(this).data('character'));
            var popupContent = `
                <strong>Name:</strong> ${characterData.nombre}<br>
                <strong>Height:</strong> ${characterData.height}<br>
                <strong>Mass:</strong> ${characterData.mass}<br>
                <strong>Hair Color:</strong> ${characterData.hair_color}<br>
                <strong>Skin Color:</strong> ${characterData.skin_color}<br>
                <strong>Eye Color:</strong> ${characterData.eye_color}<br>
                <strong>Birth Year:</strong> ${characterData.birth_year}<br>
                <strong>Gender:</strong> ${characterData.gender}<br>
            `;

            console.log(characterData)

            // Crea el popup y muestra los detalles
            var popup = $('<div class="character-details-popup">' + popupContent + '</div>');
            $(this).after(popup);

            // Posiciona el popup junto al icono
            var iconPosition = $(this).position();
            popup.css({
                top: iconPosition.top + $(this).outerHeight(),
                left: iconPosition.left
            });

            // Muestra el popup
            popup.show();

            // Cierra el popup al hacer clic en cualquier otro lugar de la página
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.character-details-popup').length && !$(e.target).closest('.character-details-icon').length) {
                    $('.character-details-popup').remove();
                }
            });
        });
    });
    </script>

    {{--  para depurar --}}
    {{--@if(isset($characters))
    @php
    dd($characters);
    @endphp
    @endif--}}


    </div>
</body>

</html>
