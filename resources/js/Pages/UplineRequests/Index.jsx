import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import { Head, useForm, router, usePage } from '@inertiajs/react';
import { Check, X, AlertCircle, RefreshCw, ArrowRight, UserCheck, ShieldAlert, Clock } from 'lucide-react';
import { useState } from 'react';

export default function Index({ requests = null, mitras = null, uplines = [], historyRequests = [], filters }) {
    const currentUser = usePage().props.auth.user;
    const isSupervisor = currentUser.role === 'supervisor';
    const isManager = currentUser.role === 'manager';

    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [selectedMitra, setSelectedMitra] = useState(null);
    
    const [selectedRequest, setSelectedRequest] = useState(null);
    const [statusFormAction, setStatusFormAction] = useState(null); // 'approved' or 'rejected'
    const [isStatusConfirmOpen, setIsStatusConfirmOpen] = useState(false);

    // Form for Supervisor to request Upline change
    const createForm = useForm({
        mitra_id: '',
        new_upline_id: '',
    });

    // Form for Manager to Approve/Reject
    const statusForm = useForm({
        status: '',
    });

    const handleCreateClick = (mitra) => {
        setSelectedMitra(mitra);
        createForm.setData({
            mitra_id: mitra.id,
            new_upline_id: '',
        });
        setIsCreateOpen(true);
    };

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('upline-requests.store'), {
            onSuccess: () => {
                createForm.reset();
                setIsCreateOpen(false);
                setSelectedMitra(null);
            }
        });
    };

    const handleStatusClick = (req, action) => {
        setSelectedRequest(req);
        setStatusFormAction(action);
        statusForm.setData('status', action);
        setIsStatusConfirmOpen(true);
    };

    const handleStatusConfirm = () => {
        statusForm.put(route('upline-requests.update', selectedRequest.id), {
            onSuccess: () => {
                setIsStatusConfirmOpen(false);
                setSelectedRequest(null);
                setStatusFormAction(null);
            }
        });
    };

    const handleSearch = (searchVal) => {
        router.get(route('upline-requests.index'), { search: searchVal }, { preserveState: true });
    };

    // --- Columns Definition for Supervisor View (Mitra List) ---
    const supervisorColumns = [
        {
            label: 'Nama',
            field: 'nama',
            render: (row) => (
                <div className="font-bold text-gray-950 dark:text-gray-50">{row.nama}</div>
            )
        },
        {
            label: 'Email',
            field: 'email',
            render: (row) => (
                <span className="text-xs text-gray-500 dark:text-gray-400">{row.email}</span>
            )
        },
        {
            label: 'Terakhir Login',
            field: 'last_login_at',
            render: (row) => row.last_login_at ? (
                <span className="text-xs font-medium text-gray-600 dark:text-gray-400">
                    {new Date(row.last_login_at).toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' })}
                </span>
            ) : <span className="text-gray-400 dark:text-gray-600 italic text-xs">Belum pernah</span>
        },
        {
            label: 'Status',
            field: 'is_active',
            render: (row) => (
                <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wider ${
                    row.is_active 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400' 
                        : 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-400'
                }`}>
                    {row.is_active ? 'Active' : 'Not Active'}
                </span>
            )
        },
        {
            label: 'Upline Saat Ini',
            field: 'upline',
            render: (row) => row.upline ? (
                <div>
                    <span className="font-semibold text-gray-900 dark:text-gray-100">{row.upline.nama}</span>
                    <span className="block text-[9px] font-bold text-brand-primary uppercase">{row.upline.role}</span>
                </div>
            ) : <span className="text-gray-400 dark:text-gray-600 italic text-xs">-</span>
        }
    ];

    // --- Columns Definition for Manager View (Requests List) ---
    const managerColumns = [
        {
            label: 'Mitra',
            field: 'mitra',
            render: (row) => (
                <div>
                    <span className="font-bold text-gray-950 dark:text-gray-50">{row.mitra?.nama}</span>
                    <span className="block text-[9px] font-bold text-brand-primary uppercase">Cabang: {row.mitra?.cabang || 'Pusat'}</span>
                </div>
            )
        },
        {
            label: 'Upline Lama',
            field: 'mitra.upline',
            render: (row) => (
                <div>
                    <span className="font-medium text-gray-700 dark:text-gray-300">{row.mitra?.upline?.nama || '-'}</span>
                    {row.mitra?.upline && (
                        <span className="block text-[9px] font-bold text-gray-400 uppercase">{row.mitra?.upline?.role}</span>
                    )}
                </div>
            )
        },
        {
            label: 'Upline Baru (Diajukan)',
            field: 'new_upline',
            render: (row) => (
                <div className="flex items-center gap-2">
                    <ArrowRight className="h-4 w-4 text-gray-400" />
                    <div>
                        <span className="font-bold text-amber-600 dark:text-amber-400">{row.new_upline?.nama}</span>
                        <span className="block text-[9px] font-bold text-gray-400 uppercase">{row.new_upline?.role}</span>
                    </div>
                </div>
            )
        },
        {
            label: 'Diajukan Oleh',
            field: 'requested_by',
            render: (row) => (
                <div>
                    <span className="font-semibold text-gray-800 dark:text-gray-200">{row.requested_by?.nama}</span>
                    <span className="block text-[9px] font-bold text-gray-400 uppercase">{row.requested_by?.role}</span>
                </div>
            )
        },
        {
            label: 'Tanggal Diajukan',
            field: 'created_at',
            render: (row) => (
                <span className="text-xs text-gray-600 dark:text-gray-400">
                    {new Date(row.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}
                </span>
            )
        },
        {
            label: 'Status',
            field: 'status',
            render: (row) => (
                <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold ${
                    row.status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400' :
                    row.status === 'rejected' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-400' :
                    'bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-400'
                }`}>
                    {row.status === 'approved' ? <Check size={12} /> : 
                     row.status === 'rejected' ? <X size={12} /> : 
                     <RefreshCw size={12} className="animate-spin" />}
                    <span className="uppercase tracking-wider text-[10px]">{row.status}</span>
                </span>
            )
        },
        {
            label: 'Diverifikasi Oleh',
            field: 'approved_by',
            render: (row) => row.approved_by ? (
                <div>
                    <span className="font-semibold text-gray-800 dark:text-gray-200">{row.approved_by?.nama}</span>
                    <span className="block text-[9px] font-bold text-gray-400 uppercase">{row.approved_by?.role}</span>
                </div>
            ) : <span className="text-gray-400 dark:text-gray-600 italic text-xs">-</span>
        }
    ];

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Upline Change Requests
                </h2>
            }
        >
            <Head title="Upline Change Requests" />

            <div className="space-y-8">
                {/* --- SUPERVISOR DASHBOARD VIEW --- */}
                {isSupervisor && mitras && (
                    <>
                        <Table
                            title="Daftar Mitra di Cabang Anda"
                            description="Kelola dan pindahkan Mitra di bawah cabang Anda ke Support lain yang aktif. Setiap perubahan memerlukan persetujuan Manager."
                            columns={supervisorColumns}
                            data={mitras}
                            onSearch={handleSearch}
                            searchValue={filters.search || ''}
                            emptyMessage="Mitra tidak ditemukan."
                        >
                            {(row) => {
                                const pendingRequest = row.upline_requests && row.upline_requests.find(r => r.status === 'pending');
                                
                                return (
                                    <div className="flex items-center justify-end">
                                        {pendingRequest ? (
                                            <span className="inline-flex items-center gap-1.5 rounded-2xl bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700 border border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30">
                                                <Clock size={12} className="animate-pulse" />
                                                <span>Menunggu Manager (&rarr; {pendingRequest.new_upline?.nama})</span>
                                            </span>
                                        ) : (
                                            <button
                                                onClick={() => handleCreateClick(row)}
                                                className="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-r from-brand-accent to-brand-primary px-3.5 py-2 text-xs font-bold text-white shadow-sm shadow-brand-primary/10 hover:opacity-95 active:scale-95 transition-all"
                                            >
                                                <RefreshCw size={12} />
                                                <span>Ganti Upline</span>
                                            </button>
                                        )}
                                    </div>
                                );
                            }}
                        </Table>

                        {/* Supervisor Request History list */}
                        <div className="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                            <div className="mb-4">
                                <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Riwayat Pengajuan Anda</h3>
                                <p className="text-xs text-gray-500 dark:text-gray-400">Daftar pemindahan Upline Mitra yang telah atau sedang Anda ajukan.</p>
                            </div>
                            
                            <div className="overflow-x-auto">
                                <table className="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                                    <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        <tr>
                                            <th className="px-6 py-4">Mitra</th>
                                            <th className="px-6 py-4">Upline Baru</th>
                                            <th className="px-6 py-4">Tanggal Diajukan</th>
                                            <th className="px-6 py-4">Status</th>
                                            <th className="px-6 py-4">Diverifikasi Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-800 dark:border-gray-800">
                                        {historyRequests.length > 0 ? (
                                            historyRequests.map((req) => (
                                                <tr key={req.id} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/20">
                                                    <td className="px-6 py-4">
                                                        <div className="font-semibold text-gray-900 dark:text-gray-100">{req.mitra?.nama}</div>
                                                    </td>
                                                    <td className="px-6 py-4 text-amber-600 dark:text-amber-400 font-semibold">{req.new_upline?.nama}</td>
                                                    <td className="px-6 py-4 text-xs text-gray-500 dark:text-gray-400">
                                                        {new Date(req.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <span className={`inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider ${
                                                            req.status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400' :
                                                            req.status === 'rejected' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-400' :
                                                            'bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-400'
                                                        }`}>
                                                            {req.status}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 text-xs text-gray-700 dark:text-gray-300 font-medium">
                                                        {req.approved_by?.nama || <span className="text-gray-400 italic font-normal">-</span>}
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan={5} className="px-6 py-8 text-center text-gray-400 italic text-xs">
                                                    Belum ada riwayat pengajuan pemindahan upline.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </>
                )}

                {/* --- MANAGER VIEW --- */}
                {isManager && requests && (
                    <Table
                        title="Persetujuan Perubahan Upline"
                        description="Daftar lengkap pengajuan perubahan upline dari Supervisor. Setujui atau tolak pengajuan untuk mengorganisir jaringan penjualan."
                        columns={managerColumns}
                        data={requests}
                        onSearch={handleSearch}
                        searchValue={filters.search || ''}
                        emptyMessage="Pengajuan tidak ditemukan."
                    >
                        {(row) => (
                            <div className="flex gap-2">
                                {row.status === 'pending' ? (
                                    <>
                                        <button
                                            onClick={() => handleStatusClick(row, 'approved')}
                                            className="inline-flex items-center gap-1 rounded-xl bg-emerald-500 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-600 active:scale-95 transition-all"
                                            title="Setujui Perubahan"
                                        >
                                            <Check size={12} />
                                            <span>Setujui</span>
                                        </button>
                                        <button
                                            onClick={() => handleStatusClick(row, 'rejected')}
                                            className="inline-flex items-center gap-1 rounded-xl bg-rose-500 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-600 active:scale-95 transition-all"
                                            title="Tolak Perubahan"
                                        >
                                            <X size={12} />
                                            <span>Tolak</span>
                                        </button>
                                    </>
                                ) : (
                                    <span className="text-xs text-gray-400 italic font-medium px-2">Selesai diproses</span>
                                )}
                            </div>
                        )}
                    </Table>
                )}
            </div>

            {/* --- SUPERVISOR: GANTI UPLINE MODAL --- */}
            {isSupervisor && (
                <Modal show={isCreateOpen} onClose={() => { setIsCreateOpen(false); setSelectedMitra(null); }} maxWidth="md">
                    <div className="p-6">
                        <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                            <h3 className="text-lg font-black text-gray-900 dark:text-gray-50 flex items-center gap-2">
                                <UserCheck className="text-brand-primary h-5 w-5" />
                                <span>Ajukan Perubahan Upline</span>
                            </h3>
                            <button onClick={() => { setIsCreateOpen(false); setSelectedMitra(null); }} className="text-gray-400 hover:text-gray-500">
                                <X size={18} />
                            </button>
                        </div>

                        {selectedMitra && (
                            <div className="mt-4 rounded-2xl bg-gray-50 p-4 dark:bg-gray-800/40">
                                <h4 className="text-xs font-bold uppercase tracking-wider text-gray-400">Detail Mitra</h4>
                                <div className="mt-1 grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-gray-800 dark:text-gray-200">
                                    <div>
                                        <span className="block text-[10px] text-gray-400">Nama Lengkap</span>
                                        <span className="font-bold">{selectedMitra.nama}</span>
                                    </div>
                                    <div>
                                        <span className="block text-[10px] text-gray-400">NIK Mitra</span>
                                        <span className="font-mono font-semibold">{selectedMitra.nik}</span>
                                    </div>
                                    <div className="col-span-2 border-t border-gray-100 pt-2 dark:border-gray-800/60 mt-1">
                                        <span className="block text-[10px] text-gray-400">Upline/Support Saat Ini</span>
                                        <span className="font-medium text-brand-primary">
                                            {selectedMitra.upline ? `${selectedMitra.upline.nama} (${selectedMitra.upline.role})` : 'Tidak memiliki upline'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        )}

                        <form onSubmit={handleCreateSubmit} className="mt-5 space-y-4">
                            <div>
                                <InputLabel htmlFor="new_upline_id" value="Pilih Upline/Support Baru" />
                                <p className="text-[10px] text-gray-400 mb-1.5">Pilihan hanya memuat Support aktif di cabang Anda ({currentUser.cabang}).</p>
                                <select
                                    id="new_upline_id"
                                    value={createForm.data.new_upline_id}
                                    onChange={(e) => createForm.setData('new_upline_id', e.target.value)}
                                    className="mt-1 block w-full rounded-2xl border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 py-2.5 text-sm transition-all"
                                    required
                                >
                                    <option value="">Pilih Support</option>
                                    {uplines.map(u => (
                                        <option key={u.id} value={u.id}>{u.nama} ({u.role})</option>
                                    ))}
                                </select>
                                <InputError message={createForm.errors.new_upline_id} className="mt-2" />
                                <InputError message={createForm.errors.mitra_id} className="mt-2" />
                            </div>

                            <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                                <button
                                    type="button"
                                    onClick={() => { setIsCreateOpen(false); setSelectedMitra(null); }}
                                    className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={createForm.processing}
                                    className="rounded-2xl bg-gradient-to-r from-brand-accent to-brand-primary px-5 py-2.5 text-sm font-bold text-white hover:opacity-90 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                                >
                                    {createForm.processing ? 'Mengirim...' : 'Ajukan Pemindahan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </Modal>
            )}

            {/* --- MANAGER: CONFIRMATION STATUS MODAL --- */}
            {isManager && (
                <Modal show={isStatusConfirmOpen} onClose={() => { setIsStatusConfirmOpen(false); setSelectedRequest(null); }}>
                    <div className="p-6">
                        <div className="flex items-center gap-3 border-b border-gray-100 pb-4 dark:border-gray-800">
                            {statusFormAction === 'approved' ? (
                                <div className="flex items-center gap-2">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400">
                                        <Check size={20} />
                                    </div>
                                    <h3 className="text-lg font-black text-emerald-600 dark:text-emerald-400">Setujui Perubahan Upline</h3>
                                </div>
                            ) : (
                                <div className="flex items-center gap-2">
                                    <div className="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400">
                                        <X size={20} />
                                    </div>
                                    <h3 className="text-lg font-black text-rose-600 dark:text-rose-400">Tolak Perubahan Upline</h3>
                                </div>
                            )}
                        </div>

                        {selectedRequest && (
                            <div className="mt-4 space-y-3">
                                <p className="text-sm text-gray-500 dark:text-gray-400">
                                    Apakah Anda yakin ingin {statusFormAction === 'approved' ? 'menyetujui' : 'menolak'} pengajuan pemindahan Mitra berikut?
                                </p>
                                
                                <div className="rounded-2xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/40 text-sm">
                                    <div className="grid grid-cols-2 gap-y-2 text-gray-800 dark:text-gray-200">
                                        <div>
                                            <span className="block text-[10px] text-gray-400">Nama Mitra</span>
                                            <span className="font-bold">{selectedRequest.mitra?.nama}</span>
                                        </div>
                                        <div>
                                            <span className="block text-[10px] text-gray-400">Diajukan Oleh</span>
                                            <span className="font-semibold">{selectedRequest.requested_by?.nama} ({selectedRequest.requested_by?.role})</span>
                                        </div>
                                        <div className="col-span-2 border-t border-gray-200/60 dark:border-gray-800 pt-2 mt-1">
                                            <div className="flex items-center gap-3">
                                                <div>
                                                    <span className="block text-[10px] text-gray-400">Upline Lama</span>
                                                    <span className="font-medium text-gray-600 dark:text-gray-400">{selectedRequest.mitra?.upline?.nama || 'Tanpa Upline'}</span>
                                                </div>
                                                <ArrowRight className="h-4 w-4 text-gray-400 mt-2" />
                                                <div>
                                                    <span className="block text-[10px] text-gray-400">Upline Baru (Diajukan)</span>
                                                    <span className="font-bold text-brand-primary">{selectedRequest.new_upline?.nama}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {statusFormAction === 'approved' && (
                                    <div className="flex gap-2 rounded-2xl bg-amber-50/70 p-3 text-xs text-amber-800 border border-amber-100 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/30">
                                        <ShieldAlert size={16} className="shrink-0 mt-0.5" />
                                        <span>Tindakan ini akan memindahkan Mitra ke dalam struktur Support yang baru dan akan langsung berlaku secara real-time.</span>
                                    </div>
                                )}
                            </div>
                        )}

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => { setIsStatusConfirmOpen(false); setSelectedRequest(null); }}
                                className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                onClick={handleStatusConfirm}
                                disabled={statusForm.processing}
                                className={`rounded-2xl px-5 py-2.5 text-sm font-bold text-white shadow-md transition-all active:scale-95 ${
                                    statusFormAction === 'approved' 
                                        ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-600/10' 
                                        : 'bg-rose-600 hover:bg-rose-700 shadow-rose-600/10'
                                }`}
                            >
                                {statusForm.processing ? 'Memproses...' : 'Ya, Konfirmasi'}
                            </button>
                        </div>
                    </div>
                </Modal>
            )}
        </AuthenticatedLayout>
    );
}
