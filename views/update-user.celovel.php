<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celovel Update</title>
</head>
<body>
    <h1>Welcome to Celovel!</h1>
    @alert('alert')
    <div>
        {{ Session::getAlert('alert') }}
    </div>
    @endalert
    @alert('success')
    <div>
        {{ Session::getAlert('success') }}
    </div>
    @endalert
    <div>
        <a href="/">Anasayfa</a>
        <a href="/add-user">Kullanıcı Ekle</a>
    </div>
    <h4>Kullanıcı Düzenle</h4>
    <form action="/update-user/{{ $user->id }}" method="POST">
        <table border="1">
            <thead>
                <tr>
                    <th>isim</th>
                    <th>email</th>
                    <th>yaş</th>
                    <th>düzenle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" value="{{ $user->fullname }}" name="fullname" placeholder="fullname">
                    </td>
                    <td>
                        <input type="email" value="{{ $user->email }}" name="email" placeholder="email">
                    </td>
                    <td>
                        <input type="number" value="{{ $user->age }}" name="age" placeholder="age">
                    </td>
                    <td><button type="submit">Düzenle</button></td>
                </tr>
            </tbody>
        </table>
    </form>
</body>
</html>