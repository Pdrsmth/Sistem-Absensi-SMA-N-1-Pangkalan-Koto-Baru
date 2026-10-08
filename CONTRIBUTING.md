# Panduan Kontribusi

Terima kasih sudah tertarik berkontribusi! 🎉

## Cara Berkontribusi

1. **Fork** repository ini.
2. Buat branch baru dari `main`:
   ```bash
   git checkout -b fitur/nama-fitur
   ```
3. Commit dengan pesan yang jelas (format di bawah).
4. **Push** lalu buka **Pull Request**.

## Format Commit

Gunakan [Conventional Commits](https://www.conventionalcommits.org/):

```
tipe(scope): deskripsi singkat

Contoh:
feat(absen): tambah rekap absen per kelas
fix(login): perbaiki redirect setelah logout
docs(readme): tambah panduan instalasi
```

Tipe: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`.

## Standar Kode

- **PHP**: indent 4 spasi, gunakan **prepared statement** (`mysqli_prepare` / PDO) untuk semua query — jangan interpolasi variabel langsung ke SQL.
- **Keamanan**: jangan commit kredensial (`config/db.php` pakai nilai lokal saja; kredensial asli di file terpisah yang di-ignore).
- Uji di PHP 7.4+ dan MySQL 5.7+ sebelum push.

## Melapor Bug

Buka **Issues** dengan format `[BUG] deskripsi`, sertakan langkah reproduksi dan screenshot kalau ada.
