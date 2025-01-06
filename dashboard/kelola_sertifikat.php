<div class="mb-6">
    <h2 class="text-lg font-bold mb-2">Daftar Peserta</h2>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border border-gray-300 px-4 py-2">No</th>
                <th class="border border-gray-300 px-4 py-2">Nama Peserta</th>
                <th class="border border-gray-300 px-4 py-2">Acara</th>
                <th class="border border-gray-300 px-4 py-2">Tanggal</th>
                <th class="border border-gray-300 px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT absensi.absensi_id, absensi.nama, events.event_name, absensi.waktu_absensi 
                                    FROM absensi 
                                    LEFT JOIN events ON absensi.event_id = events.event_id");
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td class='border border-gray-300 px-4 py-2 text-center'>{$no}</td>
                    <td class='border border-gray-300 px-4 py-2'>{$row['nama']}</td>
                    <td class='border border-gray-300 px-4 py-2'>{$row['event_name']}</td>
                    <td class='border border-gray-300 px-4 py-2'>".date('d F Y', strtotime($row['waktu_absensi']))."</td>
                    <td class='border border-gray-300 px-4 py-2 text-center'>
                        <a href='kelola_sertifikat.php?generate_id={$row['absensi_id']}' class='text-blue-500'>Generate</a> |
                        <a href='kelola_sertifikat.php?delete_id={$row['absensi_id']}' class='text-red-500'>Hapus</a>
                    </td>
                </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>
