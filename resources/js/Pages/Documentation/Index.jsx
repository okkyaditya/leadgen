import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { BookOpen, Users, Building2, UserSquare2, FileText, GitCompare, ClipboardList } from 'lucide-react';

export default function Documentation() {
    const docSections = [
        {
            title: "Dashboard",
            icon: <BookOpen className="w-6 h-6 text-brand-primary" />,
            description: "Ringkasan performa dan statistik utama aplikasi. Menampilkan total leads, status, dan konversi berdasarkan peran Anda (Admin, Manager, Supervisor, dll)."
        },
        {
            title: "Manajemen Leads",
            icon: <FileText className="w-6 h-6 text-amber-500" />,
            description: "Modul utama untuk mengelola data calon pelanggan (prospek). Anda dapat menambah, mengubah, memfilter berdasarkan cabang/produk, dan mengekspor data Leads."
        },
        {
            title: "Manajemen User",
            icon: <Users className="w-6 h-6 text-blue-500" />,
            description: "Pengelolaan pengguna internal aplikasi (Admin, Manager, Supervisor, Support). Admin dapat mengatur peran, cabang, dan atasan (supervisor) masing-masing staf."
        },
        {
            title: "Manajemen Mitra",
            icon: <UserSquare2 className="w-6 h-6 text-emerald-500" />,
            description: "Data mitra eksternal atau pihak ketiga yang memberikan referensi Leads ke dalam sistem."
        },
        {
            title: "Cabang",
            icon: <Building2 className="w-6 h-6 text-rose-500" />,
            description: "Pengaturan data cabang operasional. Setiap pengguna dan Leads akan terkait pada cabang tertentu untuk memudahkan segmentasi wilayah."
        },
        {
            title: "Upline Requests",
            icon: <GitCompare className="w-6 h-6 text-indigo-500" />,
            description: "Fitur untuk mengelola permintaan perpindahan upline atau supervisor antar tim sales/support."
        },
        {
            title: "Audit Logs",
            icon: <ClipboardList className="w-6 h-6 text-slate-500" />,
            description: "Catatan riwayat aktivitas penting (log) yang terjadi di dalam aplikasi untuk mempermudah pemantauan keamanan dan pelacakan."
        }
    ];

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50 flex items-center gap-2">
                    <BookOpen className="w-6 h-6" />
                    Dokumentasi Aplikasi
                </h2>
            }
        >
            <Head title="Dokumentasi" />

            <div className="space-y-6">
                <div className="bg-white dark:bg-gray-900 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-800">
                    <h3 className="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Selamat Datang di Panduan Pengguna Leads Tracker</h3>
                    <p className="text-gray-600 dark:text-gray-400 mb-6 leading-relaxed">
                        Aplikasi Leads Tracker dirancang untuk mempermudah manajemen data prospek, pemantauan kinerja staf, dan pengelompokan data berdasarkan Cabang serta Mitra.
                        Di halaman ini, Anda dapat menemukan penjelasan singkat mengenai setiap modul yang tersedia.
                    </p>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {docSections.map((section, idx) => (
                            <div key={idx} className="flex items-start gap-4 p-5 rounded-xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 hover:shadow-md transition-all">
                                <div className="shrink-0 p-3 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                                    {section.icon}
                                </div>
                                <div>
                                    <h4 className="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">{section.title}</h4>
                                    <p className="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        {section.description}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="bg-brand-primary/5 dark:bg-brand-primary/10 rounded-2xl p-6 border border-brand-primary/10">
                    <h4 className="font-bold text-brand-primary dark:text-brand-accent mb-2">Butuh Bantuan Lebih Lanjut?</h4>
                    <p className="text-sm text-gray-700 dark:text-gray-300">
                        Jika Anda menemukan kendala teknis atau memiliki pertanyaan terkait fitur yang belum dijelaskan di dokumentasi ini, silakan hubungi tim IT Support atau Administrator pusat.
                    </p>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
