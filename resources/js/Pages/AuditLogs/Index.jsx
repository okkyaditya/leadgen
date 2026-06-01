import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import { Head, router } from '@inertiajs/react';
import { Eye, X } from 'lucide-react';
import { useState } from 'react';

export default function Index({ logs, filters }) {
    const [selectedLog, setSelectedLog] = useState(null);
    const [isDetailsOpen, setIsDetailsOpen] = useState(false);

    const handleViewClick = (log) => {
        setSelectedLog(log);
        setIsDetailsOpen(true);
    };

    const handleSearch = (searchVal) => {
        router.get(route('audit-logs.index'), { search: searchVal }, { preserveState: true });
    };

    // Columns Definition
    const columns = [
        { 
            label: 'User', 
            field: 'user',
            render: (row) => row.user ? (
                <div>
                    <span className="font-bold text-gray-900 dark:text-gray-100">{row.user.nama}</span>
                    <span className="block text-[10px] text-gray-500 uppercase">{row.user.role} ({row.user.cabang || 'Pusat'})</span>
                </div>
            ) : <span className="text-gray-400 italic">System / Guest</span>
        },
        { 
            label: 'Aksi', 
            field: 'action',
            render: (row) => (
                <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider ${
                    row.action === 'created' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' :
                    row.action === 'updated' ? 'bg-amber-500/10 text-amber-500 border border-amber-500/20' :
                    'bg-rose-500/10 text-rose-500 border border-rose-500/20'
                }`}>
                    {row.action}
                </span>
            )
        },
        { label: 'Tipe Model', field: 'model_type' },
        { label: 'ID Record', field: 'model_id' },
        { 
            label: 'Waktu Aktivitas', 
            field: 'created_at',
            render: (row) => new Date(row.created_at).toLocaleString('id-ID')
        }
    ];

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Audit Logs
                </h2>
            }
        >
            <Head title="Audit Logs" />

            <div className="space-y-6">
                <Table
                    title="Catatan Aktivitas (Audit)"
                    description="Pantau riwayat penambahan, modifikasi, dan penghapusan data Lead serta Mitra di sistem secara real-time."
                    columns={columns}
                    data={logs}
                    onSearch={handleSearch}
                    searchValue={filters.search || ''}
                    emptyMessage="Log aktivitas tidak ditemukan."
                >
                    {(row) => (
                        <button
                            onClick={() => handleViewClick(row)}
                            className="rounded-xl p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/20 active:scale-95 transition-all flex items-center gap-1 text-xs font-bold"
                            title="Lihat Perubahan"
                        >
                            <Eye size={14} />
                            <span>Detail</span>
                        </button>
                    )}
                </Table>
            </div>

            {/* View Changes Details Modal */}
            <Modal show={isDetailsOpen} onClose={() => { setIsDetailsOpen(false); setSelectedLog(null); }} maxWidth="xl">
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Detail Audit Perubahan</h3>
                        <button onClick={() => { setIsDetailsOpen(false); setSelectedLog(null); }} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <div className="mt-4 space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        <div className="grid grid-cols-2 gap-4 text-xs bg-gray-50 dark:bg-gray-850 p-3 rounded-xl">
                            <div>
                                <span className="text-gray-400 font-semibold uppercase block">Tipe Model</span>
                                <span className="font-bold text-gray-900 dark:text-gray-100">{selectedLog?.model_type}</span>
                            </div>
                            <div>
                                <span className="text-gray-400 font-semibold uppercase block">Record ID</span>
                                <span className="font-bold text-gray-900 dark:text-gray-100">#{selectedLog?.model_id}</span>
                            </div>
                        </div>

                        {selectedLog?.changes ? (
                            <div className="space-y-3">
                                <h4 className="font-bold text-xs uppercase tracking-wider text-gray-400">Daftar Perubahan Kolom</h4>
                                <div className="space-y-2.5">
                                    {Object.entries(selectedLog.changes).map(([col, val]) => {
                                        // Check if changes are old/new object or a flat representation (e.g. from create event)
                                        const hasOldNew = val && typeof val === 'object' && 'old' in val && 'new' in val;
                                        return (
                                            <div key={col} className="rounded-xl border border-gray-100 p-3 dark:border-gray-850 bg-white dark:bg-gray-900">
                                                <span className="font-extrabold text-xs text-amber-500 uppercase">{col}</span>
                                                <div className="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                                    {hasOldNew ? (
                                                        <>
                                                            <div className="bg-rose-500/5 text-rose-600 rounded-lg p-2 border border-rose-500/10">
                                                                <span className="font-semibold block text-[10px] uppercase text-rose-500">Sebelum</span>
                                                                <span className="font-medium break-all">{val.old !== null ? String(val.old) : '-'}</span>
                                                            </div>
                                                            <div className="bg-emerald-500/5 text-emerald-600 rounded-lg p-2 border border-emerald-500/10">
                                                                <span className="font-semibold block text-[10px] uppercase text-emerald-500">Sesudah</span>
                                                                <span className="font-medium break-all">{val.new !== null ? String(val.new) : '-'}</span>
                                                            </div>
                                                        </>
                                                    ) : (
                                                        <div className="col-span-2 bg-blue-500/5 text-blue-600 rounded-lg p-2 border border-blue-500/10">
                                                            <span className="font-semibold block text-[10px] uppercase text-blue-500">Nilai Input</span>
                                                            <span className="font-medium break-all">{val !== null ? String(val) : '-'}</span>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        ) : (
                            <div className="text-center py-6 text-xs text-gray-500">
                                Tidak ada catatan perubahan data rinci (kolom) untuk aksi ini.
                            </div>
                        )}
                    </div>

                    <div className="mt-6 flex justify-end border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button
                            onClick={() => { setIsDetailsOpen(false); setSelectedLog(null); }}
                            className="rounded-2xl border border-gray-200 bg-white px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
