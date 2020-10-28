<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Users</title>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid my-5 mx-auto">
    <div class="row mb-2">
        <div class="col">
            @include('components.modalCreateUser')
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-2">
            @include('components.modalImportUserFromExcel')
        </div>
    </div>
    <div class="row">
        <div class="col">
            @if(count($users) !== 0)
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Пол</th>
                        <th>Возраст</th>
                        <th>Регион</th>
                        <th>Номера телефонов</th>
                        <th>Дата создания</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>

                            <td>{{ $user->id }}</td>
                            <td>{{ $user->username }}</td>
                            <td>
                                @if($user->gender == 1)
                                    Мужcкой
                                @else
                                    Женский
                                @endif
                            </td>
                            <td>{{ $user->age }}</td>
                            <td>{{ $user->region->name }}</td>
                            <td>
                                @foreach($user->phone as $phone)
                                    {{ $phone->number }}
                                @endforeach
                            </td>
                            <td> {{$user->created_at}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="alert alert-warning" role="alert">
                    Нет пользователей для отображения
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
