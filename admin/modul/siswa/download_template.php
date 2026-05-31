<?php
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=template_siswa.csv');

$output = fopen('php://output', 'w');

// Output column headers
fputcsv($output, array('NIS', 'Nama', 'Tempat Lahir', 'Tanggal Lahir (YYYY-MM-DD)', 'Jenis Kelamin (L/P)', 'Alamat', 'Tahun Angkatan'));

// Output sample data rows
fputcsv($output, array('12345', 'Ahmad Subarjo', 'Bogor', '2005-08-15', 'L', 'Jl. Lintas Sumbar-Riau No. 10', '2024'));
fputcsv($output, array('12346', 'Siti Aminah', 'Lima Puluh Kota', '2006-03-22', 'P', 'Jl. Sudirman No. 5', '2024'));

fclose($output);
exit();
?>
