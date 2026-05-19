<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $lecturers = [
            ['name' => 'Prof. Drs. Ir. Abdul Fadli, M.T., Ph.D.', 'email' => 'fadli@jmti.uad.ac.id', 'nidn' => '0510076701', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Adhi Prahara, S.Si., M.Cs.', 'email' => 'adhi.prahara@tif.uad.ac.id', 'nidn' => '0524118801', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Ahmad Azhari, S.Kom., M.Eng.', 'email' => 'ahmad.azhari@tif.uad.ac.id', 'nidn' => '0505118901', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ali Tarmuji, S.T., M.Cs.', 'email' => 'alitarmuji@tif.uad.ac.id', 'nidn' => '0014107301', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Andri Pranolo, S.Kom., M.Cs., Ph.D.', 'email' => 'andri.pranolo@tif.uad.ac.id', 'nidn' => '0505038301', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Anna Hendri Soleliza Jones, S.Kom., M.Cs.', 'email' => 'annahendri@tif.uad.ac.id', 'nidn' => '0522018302', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Prof. Ir. Anton Yudhana, S.T., M.T., Ph.D.', 'email' => 'eyudhana@mti.uad.ac.id', 'nidn' => '0508087601', 'prodi' => 'Magister Informatika'],
            ['name' => 'Dr. Ardiansyah, S.T., M.Cs.', 'email' => 'ardiansyah@tif.uad.ac.id', 'nidn' => '0523077902', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dr. Ir. Ardi Pujiyanta, M.T.', 'email' => 'ardipujiyanta@tif.uad.ac.id', 'nidn' => '0529056601', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Arfiani Nur Khusna, S.T., M.Kom.', 'email' => 'arfiani.khusna@tif.uad.ac.id', 'nidn' => '0526018502', 'prodi' => 'Teknik Informatika'],
            ['name' => "Bambang Robi'in, S.T., M.T.", 'email' => 'bambang.robiin@tif.uad.ac.id', 'nidn' => '0020077901', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dr. Dewi Pramudi Ismi, S.T., M.CompSc.', 'email' => 'dewi.ismi@tif.uad.ac.id', 'nidn' => '0521128502', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dewi Soyusiawati, S.T., M.T.', 'email' => 'dewi.soyusiawati@tif.uad.ac.id', 'nidn' => '0530077601', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Dinan Yulianto, S.T., M.Eng.', 'email' => 'dinan.yulianto@tif.uad.ac.id', 'nidn' => '0514079201', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dwi Normawati, S.T., M.Eng.', 'email' => 'dwi.normawati@tif.uad.ac.id', 'nidn' => '0504088601', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Eko Aribowo, S.T., M.Kom.', 'email' => 'ekoab@tif.uad.ac.id', 'nidn' => '0006027001', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Faisal Fajri Rahani S.Si., M.Cs.', 'email' => 'faisal.fajri@tif.uad.ac.id', 'nidn' => '0506079301', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Fitfin Noviyanto, S.T., M.Cs.', 'email' => 'fitfin.noviyanto@tif.uad.ac.id', 'nidn' => '0015118001', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Guntur Maulana Zamroni, B.Sc., M.Kom.', 'email' => 'guntur.zamroni@tif.uad.ac.id', 'nidn' => '0509038402', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Herman, S.Kom., M.Sc., Ph.D.', 'email' => 'hermankaha@mti.uad.ac.id', 'nidn' => '0531127302', 'prodi' => 'Magister Informatika'],
            ['name' => 'Ir. Herman Yuliansyah, S.T., M.Eng., Ph.D.', 'email' => 'herman.yuliansyah@tif.uad.ac.id', 'nidn' => '0512078304', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Ika Arfiani, S.T., M.Cs.', 'email' => 'ika.arfiani@tif.uad.ac.id', 'nidn' => '0520098702', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Prof. Dr. Ir. Imam Riadi, M.Kom.', 'email' => 'imam.riadi@mti.uad.ac.id', 'nidn' => '0510088001', 'prodi' => 'Magister Informatika'],
            ['name' => 'Jihad Rahmawan, S.T., M.Sc.', 'email' => 'jihad@tif.uad.ac.id', 'nidn' => null, 'prodi' => 'Teknik Informatika'],
            ['name' => 'Jefree Fahana, S.T., M.Kom.', 'email' => 'jefree.fahana@tif.uad.ac.id', 'nidn' => '0528058401', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Lisna Zahrotun, S.T., M.Cs.', 'email' => 'lisna.zahrotun@tif.uad.ac.id', 'nidn' => '0511098401', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Miftahurrahma Rosyda, S.Kom., M.Eng.', 'email' => 'miftahurrahma.rosyda@tif.uad.ac.id', 'nidn' => '0515069001', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Muhammad Aziz, S.T., M.Cs., Ph.D.', 'email' => 'moch.aziz@tif.uad.ac.id', 'nidn' => '0516017701', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dr. Eng. Ir. Muhammad Kunta Biddinika, S.T., M. Eng.', 'email' => 'muhammad.kunta@mti.uad.ac.id', 'nidn' => '0526107801', 'prodi' => 'Magister Informatika'],
            ['name' => 'Murein Miksa Mardhia, S.T., M.T.', 'email' => 'murein.miksa@tif.uad.ac.id', 'nidn' => '0519108901', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Dr. Murinto, S.Si., M.Kom.', 'email' => 'murintokusno@tif.uad.ac.id', 'nidn' => '0510077302', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Mushlihudin, S.T., M.T.', 'email' => 'mushlihudin@tif.uad.ac.id', 'nidn' => '0506016701', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ninda Khoirunnisa, S.T., M.Sc.', 'email' => 'ninda@tif.uad.ac.id', 'nidn' => null, 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Nuril Anwar, S.T., M.Kom.', 'email' => 'nuril.anwar@tif.uad.ac.id', 'nidn' => '0509048901', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Nur Rochmah Dyah Puji Astuti, S.T., M.Kom.', 'email' => 'rochmahdyah@tif.uad.ac.id', 'nidn' => '0019087601', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Rusydi Umar, S.T., M.T., Ph.D.', 'email' => 'rusydi.umar@tif.uad.ac.id', 'nidn' => '0507087202', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Sheraton Pawestri, S.Kom., M.Cs.', 'email' => 'sheraton@tif.uad.ac.id', 'nidn' => null, 'prodi' => 'Teknik Informatika'],
            ['name' => 'Ir. Sri Winiarti, S.T., M.Cs.', 'email' => 'sri.winiarti@tif.uad.ac.id', 'nidn' => '0516127501', 'prodi' => 'Teknik Informatika'],
            ['name' => 'Prof. Ir. Sunardi, S.T., M.T., Ph.D.', 'email' => 'sunardi@mti.uad.ac.id', 'nidn' => '0521057401', 'prodi' => 'Magister Informatika'],
            ['name' => 'Supriyanto, S.T., M.T.', 'email' => 'supriyanto@tif.uad.ac.id', 'nidn' => '0523068801', 'prodi' => 'Teknik Informatika'],
        ];

        foreach ($lecturers as $lecturer) {
            User::updateOrCreate(
                ['email' => $lecturer['email']],
                [
                    'name' => $lecturer['name'],
                    'phone' => $lecturer['phone'] ?? null,
                    'nim' => null,
                    'nidn' => $lecturer['nidn'],
                    'role' => 'dosen',
                    'prodi' => $lecturer['prodi'] ?? 'Teknik Informatika',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
