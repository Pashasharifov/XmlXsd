<!DOCTYPE html>
<html lang="az">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Fayl Yükləmə</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Excel (.xlsx) Fayl Yükləmə</h5>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="file" class="form-label fw-semibold">Excel fayl seç:</label>
                                <input type="file" name="file" id="file"
                                    class="form-control @error('file') is-invalid @enderror" accept=".xlsx" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-upload"></i> Faylı yüklə
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Upload edilmiş faylların siyahısı --}}
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Yüklənmiş fayllar</h6>
                    </div>

                    <div class="card-body">
                        @if (isset($uploads))
                            <p class="mb-2">Ümumi yüklənmiş fayl sayı: <strong>{{ $uploads->count() }}</strong></p>
                            @if ($uploads->isEmpty())
                                <p class="text-muted mb-0">Hələ heç bir fayl yüklənməyib.</p>
                            @else
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Fayl adı</th>
                                            <th>Status</th>
                                            <th>Tarix</th>
                                            <th>Əməliyyat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uploads as $upload)
                                            <tr id="upload-{{ $upload->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $upload->filename }}</td>
                                                <td class="status-badge">
                                                    @switch($upload->status)
                                                        @case('pending')
                                                            <span class="badge bg-warning text-dark">Gözləmədə</span>
                                                        @break

                                                        @case('processing')
                                                            <span class="badge bg-info text-dark">Emal olunur</span>
                                                        @break

                                                        @case('success')
                                                            <span class="badge bg-success">Uğurlu</span>
                                                        @break

                                                        @case('error')
                                                            <span class="badge bg-danger">Xəta</span>
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $upload->created_at->format('d.m.Y H:i') }}</td>
                                                <td class="operation-cell">
                                                    @if ($upload->status === 'success')
                                                        <a href="{{ route('upload.download', $upload->id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            Yüklə (XML)
                                                        </a>
                                                    @elseif($upload->status === 'error')
                                                        <button class="btn btn-sm btn-outline-danger"
                                                            onclick="alert('Xəta: {{ $upload->error_message }}')">
                                                            Xəta bax
                                                        </button>
                                                    @else
                                                        <small class="text-muted">Hazır deyil</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        setInterval(() => {
            $.get('{{ route('upload.statuses') }}', function(data) {
                data.forEach(item => {
                    const row = $(`#upload-${item.id}`); // ⬅️ row burada təyin olunur
                    if (!row.length) return;

                    // STATUS badge yenilə
                    const badgeCell = row.find('.status-badge');
                    let badgeHtml = '';
                    switch (item.status) {
                        case 'pending':
                            badgeHtml = '<span class="badge bg-warning text-dark">Gözləmədə</span>';
                            break;
                        case 'processing':
                            badgeHtml = '<span class="badge bg-info text-dark">Emal olunur</span>';
                            break;
                        case 'success':
                            badgeHtml = '<span class="badge bg-success">Uğurlu</span>';
                            break;
                        case 'error':
                            badgeHtml = '<span class="badge bg-danger">Xəta</span>';
                            break;
                    }
                    badgeCell.html(badgeHtml);

                    // ƏMƏLİYYAT sütununu yenilə
                    const actionCell = row.find('.operation-cell');
                    let actionHtml = '';

                    if (item.status === 'success') {
                        actionHtml = `
                    <a href="/uploads/${item.id}/download" 
                       class="btn btn-sm btn-outline-primary">
                        Yüklə (XML)
                    </a>`;
                    } else if (item.status === 'error') {
                        actionHtml = `
                    <button class="btn btn-sm btn-outline-danger" 
                            onclick="alert('Xəta: ${item.error_message || 'Naməlum xəta'}')">
                        Xəta bax
                    </button>`;
                    } else {
                        actionHtml = '<small class="text-muted">Hazır deyil</small>';
                    }

                    actionCell.html(actionHtml);
                });
            });
        }, 5000);
    </script>

</body>
</html>
