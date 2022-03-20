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
    <h4>Tüm Kullanıcılar</h4>
    <table border="1">
        <thead>
            <tr>
                <th>isim</th>
                <th>email</th>
                <th>yaş</th>
                <th>düzenle</th>
                <th>sil</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $item)
                <tr>
                    <td>{{ $item->fullname }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->age }}</td>
                    <td><a href="/update-user/{{ $item->id }}">Düzenle</a></td>
                    <td><a href="/delete-user/{{ $item->id }}">Sil</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>