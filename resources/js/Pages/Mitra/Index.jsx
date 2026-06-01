import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, useForm, router, usePage } from '@inertiajs/react';
import { Edit2, Trash2, X, Check, AlertCircle, Download } from 'lucide-react';
import { useState } from 'react';

export default function Index({ mitras, uplines, products, filters }) {
    const currentUser = usePage().props.auth.user;
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [selectedMitra, setSelectedMitra] = useState(null);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);

    // Initial state matching Mitra form fields
    const initialFormState = {
        nama: '',
        nik: '',
        telepon: '',
        email: '',
        password: '',
        profesi: '',
        tanggal_lahir: '',
        domisili: '',
        supervisor_id: currentUser.role === 'supervisor' || currentUser.role === 'support' ? currentUser.id : '',
        is_active: 1,
        is_active_reason: '',
    };

    const createForm = useForm(initialFormState);
    const editForm = useForm({ ...initialFormState, id: null });

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('mitra.store'), {
            onSuccess: () => {
                createForm.reset();
                setIsCreateOpen(false);
            }
        });
    };

    const handleEditClick = (mitra) => {
        setSelectedMitra(mitra);
        editForm.setData({
            nama: mitra.nama || '',
            nik: mitra.nik || '',
            telepon: mitra.telepon || '',
            email: mitra.email || '',
            password: '', // empty on edit
            profesi: mitra.profesi || '',
            tanggal_lahir: mitra.tanggal_lahir ? mitra.tanggal_lahir.split('T')[0] : '',
            domisili: mitra.domisili || '',
            supervisor_id: mitra.supervisor_id || '',
            is_active: mitra.is_active ? 1 : 0,
            is_active_reason: mitra.is_active_reason || '',
        });
        setIsEditOpen(true);
    };

    const handleEditSubmit = (e) => {
        e.preventDefault();
        editForm.put(route('mitra.update', selectedMitra.id), {
            onSuccess: () => {
                setIsEditOpen(false);
                setSelectedMitra(null);
            }
        });
    };

    const handleDeleteClick = (mitra) => {
        setSelectedMitra(mitra);
        setIsDeleteOpen(true);
    };

    const handleDeleteConfirm = () => {
        router.delete(route('mitra.destroy', selectedMitra.id), {
            onSuccess: () => {
                setIsDeleteOpen(false);
                setSelectedMitra(null);
            }
        });
    };

    const handleSearch = (searchVal) => {
        router.get(route('mitra.index'), { 
            search: searchVal,
            produk: filters.produk || ''
        }, { preserveState: true });
    };

    const handleFilterChange = (key, value) => {
        router.get(route('mitra.index'), {
            ...filters,
            [key]: value
        }, { preserveState: true });
    };

    const handleExport = () => {
        const params = new URLSearchParams();
        if (filters.search) params.append('search', filters.search);
        if (filters.produk) params.append('produk', filters.produk);
        
        window.open(route('mitra.export') + '?' + params.toString());
    };

    const filterControls = (
        <div className="flex flex-wrap sm:flex-nowrap gap-2 w-full sm:w-auto">
            <select
                value={filters.produk || ''}
                onChange={(e) => handleFilterChange('produk', e.target.value)}
                className="flex-1 sm:flex-none rounded-2xl border-gray-200 bg-gray-50 text-sm focus:border-brand-accent focus:bg-white focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-800 dark:focus:border-brand-accent dark:focus:bg-gray-900 transition-all py-2.5 px-4"
            >
                <option value="">Semua Produk</option>
                {products.map(p => (
                    <option key={p} value={p}>{p}</option>
                ))}
            </select>

            <button
                onClick={handleExport}
                className="flex-1 sm:flex-none flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 active:scale-95 transition-all dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 shadow-sm"
                title="Export Excel"
            >
                <Download size={16} />
                <span>Export Excel</span>
            </button>
        </div>
    );

    // Columns Definition
    const columns = [
        { label: 'Nama', field: 'nama' },
        { label: 'Telepon', field: 'telepon' },
        { label: 'Email', field: 'email' },
        { 
            label: 'Upline / Leader', 
            field: 'upline',
            render: (row) => row.upline ? (
                <div>
                    <span className="font-bold text-gray-900 dark:text-gray-100">{row.upline.nama}</span>
                    <span className="block text-[10px] uppercase text-gray-500">{row.upline.role} ({row.upline.cabang || 'Pusat'})</span>
                </div>
            ) : '-'
        },
        { 
            label: 'Status', 
            field: 'is_active',
            render: (row) => (
                <div className="flex flex-col gap-0.5">
                    <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-bold w-max ${
                        row.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-400'
                    }`}>
                        {row.is_active ? <Check size={12} /> : <AlertCircle size={12} />}
                        {row.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                    {!row.is_active && row.is_active_reason && (
                        <span className="text-[10px] text-rose-500 italic max-w-[120px] truncate" title={row.is_active_reason}>
                            Reason: {row.is_active_reason}
                        </span>
                    )}
                </div>
            )
        },
        { 
            label: 'Terakhir Login', 
            field: 'last_login_at',
            render: (row) => row.last_login_at ? new Date(row.last_login_at).toLocaleString('id-ID') : '-'
        }
    ];

    // Check if current user is restricted upline
    const isRestrictedUpline = currentUser.role === 'supervisor' || currentUser.role === 'support';

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Manajemen Mitra
                </h2>
            }
        >
            <Head title="Manajemen Mitra" />

            <div className="space-y-6">
                <Table
                    title="Daftar Mitra Jaringan"
                    description="Kelola data mitra pemasaran eksternal yang berada di bawah jaringan upline."
                    columns={columns}
                    data={mitras}
                    onSearch={handleSearch}
                    searchValue={filters.search || ''}
                    onAdd={() => setIsCreateOpen(true)}
                    addLabel="Tambah Mitra"
                    filters={filterControls}
                    emptyMessage="Mitra tidak ditemukan."
                >
                    {(row) => (
                        <>
                            <button
                                onClick={() => handleEditClick(row)}
                                className="rounded-xl p-2 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/20 active:scale-95 transition-all"
                                title="Edit"
                            >
                                <Edit2 size={16} />
                            </button>
                            <button
                                onClick={() => handleDeleteClick(row)}
                                className="rounded-xl p-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/20 active:scale-95 transition-all"
                                title="Delete"
                            >
                                <Trash2 size={16} />
                            </button>
                        </>
                    )}
                </Table>
            </div>

            {/* Create Modal */}
            <Modal show={isCreateOpen} onClose={() => setIsCreateOpen(false)} maxWidth="2xl">
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Tambah Mitra Baru</h3>
                        <button onClick={() => setIsCreateOpen(false)} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleCreateSubmit} className="mt-4 space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel htmlFor="nama" value="Nama Lengkap" />
                                <TextInput
                                    id="nama"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={createForm.data.nama}
                                    onChange={(e) => createForm.setData('nama', e.target.value)}
                                    placeholder="Nama mitra..."
                                    required
                                />
                                <InputError message={createForm.errors.nama} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="nik" value="NIK" />
                                <TextInput
                                    id="nik"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={createForm.data.nik}
                                    onChange={(e) => createForm.setData('nik', e.target.value)}
                                    placeholder="NIK 16 digit..."
                                    maxLength={16}
                                    required
                                />
                                <InputError message={createForm.errors.nik} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="telepon" value="Telepon" />
                                <TextInput
                                    id="telepon"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={createForm.data.telepon}
                                    onChange={(e) => createForm.setData('telepon', e.target.value)}
                                    placeholder="Nomor telepon..."
                                    maxLength={16}
                                    required
                                />
                                <InputError message={createForm.errors.telepon} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    value={createForm.data.email}
                                    onChange={(e) => createForm.setData('email', e.target.value)}
                                    placeholder="Email aktif..."
                                    required
                                />
                                <InputError message={createForm.errors.email} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    className="mt-1 block w-full"
                                    value={createForm.data.password}
                                    onChange={(e) => createForm.setData('password', e.target.value)}
                                    placeholder="Password login..."
                                    required
                                />
                                <InputError message={createForm.errors.password} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="profesi" value="Profesi" />
                                <TextInput
                                    id="profesi"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={createForm.data.profesi}
                                    onChange={(e) => createForm.setData('profesi', e.target.value)}
                                    placeholder="Pekerjaan saat ini..."
                                    required
                                />
                                <InputError message={createForm.errors.profesi} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="tanggal_lahir" value="Tanggal Lahir" />
                                <TextInput
                                    id="tanggal_lahir"
                                    type="date"
                                    className="mt-1 block w-full"
                                    value={createForm.data.tanggal_lahir}
                                    onChange={(e) => createForm.setData('tanggal_lahir', e.target.value)}
                                    required
                                />
                                <InputError message={createForm.errors.tanggal_lahir} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="domisili" value="Domisili" />
                                <TextInput
                                    id="domisili"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={createForm.data.domisili}
                                    onChange={(e) => createForm.setData('domisili', e.target.value)}
                                    placeholder="Alamat domisili..."
                                    required
                                />
                                <InputError message={createForm.errors.domisili} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="supervisor_id" value="Upline / Leader Jaringan" />
                                <select
                                    id="supervisor_id"
                                    value={createForm.data.supervisor_id}
                                    onChange={(e) => createForm.setData('supervisor_id', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    disabled={isRestrictedUpline}
                                    required
                                >
                                    {isRestrictedUpline ? (
                                        <option value={currentUser.id}>{currentUser.nama}</option>
                                    ) : (
                                        <>
                                            <option value="">Pilih Upline</option>
                                            {uplines.map(u => (
                                                <option key={u.id} value={u.id}>{u.nama} ({u.role} - {u.cabang})</option>
                                            ))}
                                        </>
                                    )}
                                </select>
                                <InputError message={createForm.errors.supervisor_id} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="is_active" value="Status Aktif" />
                                <select
                                    id="is_active"
                                    value={createForm.data.is_active}
                                    onChange={(e) => createForm.setData('is_active', parseInt(e.target.value))}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    disabled={currentUser.role === 'support'}
                                >
                                    <option value={1}>Aktif</option>
                                    <option value={0}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => setIsCreateOpen(false)}
                                className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                disabled={createForm.processing}
                                className="rounded-2xl bg-gradient-to-r from-brand-accent to-brand-primary px-4 py-2.5 text-sm font-bold text-white hover:opacity-90 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                            >
                                Simpan Mitra
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Modal */}
            <Modal show={isEditOpen} onClose={() => { setIsEditOpen(false); setSelectedMitra(null); }} maxWidth="2xl">
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Perbarui Detail Mitra</h3>
                        <button onClick={() => { setIsEditOpen(false); setSelectedMitra(null); }} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleEditSubmit} className="mt-4 space-y-4">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel htmlFor="edit_nama" value="Nama Lengkap" />
                                <TextInput
                                    id="edit_nama"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={editForm.data.nama}
                                    onChange={(e) => editForm.setData('nama', e.target.value)}
                                    placeholder="Nama mitra..."
                                    required
                                />
                                <InputError message={editForm.errors.nama} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_nik" value="NIK" />
                                <TextInput
                                    id="edit_nik"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={editForm.data.nik}
                                    onChange={(e) => editForm.setData('nik', e.target.value)}
                                    placeholder="NIK..."
                                    maxLength={16}
                                    required
                                />
                                <InputError message={editForm.errors.nik} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_telepon" value="Telepon" />
                                <TextInput
                                    id="edit_telepon"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={editForm.data.telepon}
                                    onChange={(e) => editForm.setData('telepon', e.target.value)}
                                    placeholder="Nomor telepon..."
                                    maxLength={16}
                                    required
                                />
                                <InputError message={editForm.errors.telepon} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_email" value="Email" />
                                <TextInput
                                    id="edit_email"
                                    type="email"
                                    className="mt-1 block w-full"
                                    value={editForm.data.email}
                                    onChange={(e) => editForm.setData('email', e.target.value)}
                                    placeholder="Email..."
                                    required
                                />
                                <InputError message={editForm.errors.email} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_password" value="Password (Kosongkan jika tetap)" />
                                <TextInput
                                    id="edit_password"
                                    type="password"
                                    className="mt-1 block w-full"
                                    value={editForm.data.password}
                                    onChange={(e) => editForm.setData('password', e.target.value)}
                                    placeholder="Masukkan password baru..."
                                />
                                <InputError message={editForm.errors.password} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_profesi" value="Profesi" />
                                <TextInput
                                    id="edit_profesi"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={editForm.data.profesi}
                                    onChange={(e) => editForm.setData('profesi', e.target.value)}
                                    placeholder="Pekerjaan..."
                                    required
                                />
                                <InputError message={editForm.errors.profesi} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_tanggal_lahir" value="Tanggal Lahir" />
                                <TextInput
                                    id="edit_tanggal_lahir"
                                    type="date"
                                    className="mt-1 block w-full"
                                    value={editForm.data.tanggal_lahir}
                                    onChange={(e) => editForm.setData('tanggal_lahir', e.target.value)}
                                    required
                                />
                                <InputError message={editForm.errors.tanggal_lahir} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_domisili" value="Domisili" />
                                <TextInput
                                    id="edit_domisili"
                                    type="text"
                                    className="mt-1 block w-full"
                                    value={editForm.data.domisili}
                                    onChange={(e) => editForm.setData('domisili', e.target.value)}
                                    placeholder="Alamat domisili..."
                                    required
                                />
                                <InputError message={editForm.errors.domisili} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_supervisor_id" value="Upline / Leader Jaringan" />
                                <select
                                    id="edit_supervisor_id"
                                    value={editForm.data.supervisor_id}
                                    onChange={(e) => editForm.setData('supervisor_id', e.target.value)}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    disabled={isRestrictedUpline}
                                    required
                                >
                                    {isRestrictedUpline ? (
                                        <option value={currentUser.id}>{currentUser.nama}</option>
                                    ) : (
                                        <>
                                            <option value="">Pilih Upline</option>
                                            {uplines.map(u => (
                                                <option key={u.id} value={u.id}>{u.nama} ({u.role} - {u.cabang})</option>
                                            ))}
                                        </>
                                    )}
                                </select>
                                <InputError message={editForm.errors.supervisor_id} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_is_active" value="Status Aktif" />
                                <select
                                    id="edit_is_active"
                                    value={editForm.data.is_active}
                                    onChange={(e) => editForm.setData('is_active', parseInt(e.target.value))}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    disabled={currentUser.role === 'support'}
                                >
                                    <option value={1}>Aktif</option>
                                    <option value={0}>Nonaktif</option>
                                </select>
                            </div>

                            {editForm.data.is_active === 0 && (
                                <div className="md:col-span-2">
                                    <InputLabel htmlFor="edit_is_active_reason" value="Alasan Nonaktif" />
                                    <TextInput
                                        id="edit_is_active_reason"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={editForm.data.is_active_reason}
                                        onChange={(e) => editForm.setData('is_active_reason', e.target.value)}
                                        placeholder="Berikan alasan penonaktifan akun..."
                                        required={editForm.data.is_active === 0}
                                        disabled={currentUser.role === 'support'}
                                    />
                                    <InputError message={editForm.errors.is_active_reason} className="mt-2" />
                                </div>
                            )}
                        </div>

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => { setIsEditOpen(false); setSelectedMitra(null); }}
                                className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                disabled={editForm.processing}
                                className="rounded-2xl bg-gradient-to-r from-brand-accent to-brand-primary px-4 py-2.5 text-sm font-bold text-white hover:opacity-90 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                            >
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Delete Modal */}
            <Modal show={isDeleteOpen} onClose={() => { setIsDeleteOpen(false); setSelectedMitra(null); }}>
                <div className="p-6">
                    <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Hapus Akun Mitra</h3>
                    <p className="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus akun mitra <span className="font-bold text-gray-900 dark:text-gray-100">{selectedMitra?.nama}</span>? Tindakan ini akan menghapus seluruh data keanggotaannya dari jaringan.
                    </p>

                    <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button
                            type="button"
                            onClick={() => { setIsDeleteOpen(false); setSelectedMitra(null); }}
                            className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleDeleteConfirm}
                            className="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                        >
                            Ya, Hapus Mitra
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
