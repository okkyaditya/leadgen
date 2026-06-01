import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, useForm, router } from '@inertiajs/react';
import { Edit2, Trash2, X, Check, AlertCircle } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Index({ users, cabangs, supervisors, filters }) {
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [selectedUser, setSelectedUser] = useState(null);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);

    // Form structure matching backend
    const initialFormState = {
        nama: '',
        nik: '',
        telepon: '',
        email: '',
        role: 'supervisor',
        cabang: cabangs[0] || 'Pusat',
        hire_date: new Date().toISOString().split('T')[0],
        password: '',
        is_active: 1,
        supervisor_id: '',
    };

    const createForm = useForm(initialFormState);
    const editForm = useForm({ ...initialFormState, id: null });

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('users.store'), {
            onSuccess: () => {
                createForm.reset();
                setIsCreateOpen(false);
            }
        });
    };

    const handleEditClick = (user) => {
        setSelectedUser(user);
        editForm.setData({
            nama: user.nama || '',
            nik: user.nik || '',
            telepon: user.telepon || '',
            email: user.email || '',
            role: user.role || 'supervisor',
            cabang: user.cabang || 'Pusat',
            hire_date: user.hire_date ? user.hire_date.split('T')[0] : '',
            password: '', // blank on edit
            is_active: user.is_active ? 1 : 0,
            supervisor_id: user.supervisor_id || '',
        });
        setIsEditOpen(true);
    };

    const handleEditSubmit = (e) => {
        e.preventDefault();
        editForm.put(route('users.update', selectedUser.id), {
            onSuccess: () => {
                setIsEditOpen(false);
                setSelectedUser(null);
            }
        });
    };

    const handleDeleteClick = (user) => {
        setSelectedUser(user);
        setIsDeleteOpen(true);
    };

    const handleDeleteConfirm = () => {
        router.delete(route('users.destroy', selectedUser.id), {
            onSuccess: () => {
                setIsDeleteOpen(false);
                setSelectedUser(null);
            }
        });
    };

    const handleSearch = (searchVal) => {
        router.get(route('users.index'), { 
            search: searchVal,
            cabang: filters.cabang || ''
        }, { preserveState: true });
    };

    const handleCabangFilter = (e) => {
        router.get(route('users.index'), { 
            search: filters.search || '',
            cabang: e.target.value 
        }, { preserveState: true });
    };

    // Filter supervisors options by selected cabang
    const getFilteredSupervisors = (selectedCabang) => {
        return supervisors.filter(s => s.cabang === selectedCabang);
    };

    // Columns Definition
    const columns = [
        { label: 'Nama', field: 'nama' },
        { label: 'Telepon', field: 'telepon' },
        { label: 'Email', field: 'email' },
        { 
            label: 'Role', 
            field: 'role',
            render: (row) => (
                <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider border ${
                    row.role === 'admin' ? 'bg-brand-accent/50/10 text-brand-primary border-indigo-500/20' :
                    row.role === 'manager' ? 'bg-violet-500/10 text-violet-500 border-violet-500/20' :
                    row.role === 'supervisor' ? 'bg-amber-500/10 text-amber-500 border-amber-500/20' :
                    'bg-blue-500/10 text-blue-500 border-blue-500/20'
                }`}>
                    {row.role}
                </span>
            )
        },
        { label: 'Cabang', field: 'cabang' },
        { 
            label: 'Supervisor', 
            field: 'supervisor',
            render: (row) => row.supervisor?.nama || '-'
        },
        { 
            label: 'Status', 
            field: 'is_active',
            render: (row) => (
                <span className={`inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-bold ${
                    row.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-400'
                }`}>
                    {row.is_active ? <Check size={12} /> : <AlertCircle size={12} />}
                    {row.is_active ? 'Aktif' : 'Nonaktif'}
                </span>
            )
        }
    ];

    const filterDropdown = (
        <select
            value={filters.cabang || ''}
            onChange={handleCabangFilter}
            className="rounded-2xl border-gray-200 bg-gray-50 text-sm focus:border-amber-500 focus:bg-white focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-amber-500 dark:focus:bg-gray-900 transition-all py-2.5 pl-4 pr-10"
        >
            <option value="">Semua Cabang</option>
            {cabangs.map(c => (
                <option key={c} value={c}>{c}</option>
            ))}
        </select>
    );

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Manajemen User
                </h2>
            }
        >
            <Head title="Manajemen User" />

            <div className="space-y-6">
                <Table
                    title="Daftar Pengguna"
                    description="Kelola seluruh akun internal (Admin, Manager, Supervisor, Support) dalam sistem."
                    columns={columns}
                    data={users}
                    onSearch={handleSearch}
                    searchValue={filters.search || ''}
                    onAdd={() => setIsCreateOpen(true)}
                    addLabel="Tambah User"
                    filters={filterDropdown}
                    emptyMessage="User tidak ditemukan."
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
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Tambah User Baru</h3>
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
                                    placeholder="Masukkan nama lengkap..."
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
                                    placeholder="Masukkan NIK 16 digit..."
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
                                    placeholder="Contoh: 0812345..."
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
                                    placeholder="Masukkan email aktif..."
                                    required
                                />
                                <InputError message={createForm.errors.email} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="role" value="Role" />
                                <select
                                    id="role"
                                    value={createForm.data.role}
                                    onChange={(e) => {
                                        createForm.setData(data => ({
                                            ...data,
                                            role: e.target.value,
                                            supervisor_id: e.target.value !== 'support' ? '' : data.supervisor_id
                                        }));
                                    }}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="support">Support</option>
                                </select>
                                <InputError message={createForm.errors.role} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="cabang" value="Cabang" />
                                <select
                                    id="cabang"
                                    value={createForm.data.cabang}
                                    onChange={(e) => {
                                        createForm.setData(data => ({
                                            ...data,
                                            cabang: e.target.value,
                                            supervisor_id: '' // reset on cabang change to ensure matches
                                        }));
                                    }}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    {cabangs.map(c => (
                                        <option key={c} value={c}>{c}</option>
                                    ))}
                                </select>
                                <InputError message={createForm.errors.cabang} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="hire_date" value="Hire Date" />
                                <TextInput
                                    id="hire_date"
                                    type="date"
                                    className="mt-1 block w-full"
                                    value={createForm.data.hire_date}
                                    onChange={(e) => createForm.setData('hire_date', e.target.value)}
                                    required
                                />
                                <InputError message={createForm.errors.hire_date} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    className="mt-1 block w-full"
                                    value={createForm.data.password}
                                    onChange={(e) => createForm.setData('password', e.target.value)}
                                    placeholder="Masukkan password akun..."
                                    required
                                />
                                <InputError message={createForm.errors.password} className="mt-2" />
                            </div>

                            {createForm.data.role === 'support' && (
                                <div className="md:col-span-2">
                                    <InputLabel htmlFor="supervisor_id" value="Supervisor (Cabang yang sama)" />
                                    <select
                                        id="supervisor_id"
                                        value={createForm.data.supervisor_id}
                                        onChange={(e) => createForm.setData('supervisor_id', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        required
                                    >
                                        <option value="">Pilih Supervisor</option>
                                        {getFilteredSupervisors(createForm.data.cabang).map(s => (
                                            <option key={s.id} value={s.id}>{s.nama}</option>
                                        ))}
                                    </select>
                                    {getFilteredSupervisors(createForm.data.cabang).length === 0 && (
                                        <p className="mt-1 text-xs text-rose-500">Tidak ada supervisor aktif di cabang {createForm.data.cabang}.</p>
                                    )}
                                    <InputError message={createForm.errors.supervisor_id} className="mt-2" />
                                </div>
                            )}

                            <div>
                                <InputLabel htmlFor="is_active" value="Status Aktif" />
                                <select
                                    id="is_active"
                                    value={createForm.data.is_active}
                                    onChange={(e) => createForm.setData('is_active', parseInt(e.target.value))}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
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
                                Simpan User
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Modal */}
            <Modal show={isEditOpen} onClose={() => { setIsEditOpen(false); setSelectedUser(null); }} maxWidth="2xl">
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Perbarui Detail User</h3>
                        <button onClick={() => { setIsEditOpen(false); setSelectedUser(null); }} className="text-gray-400 hover:text-gray-500">
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
                                    placeholder="Masukkan nama lengkap..."
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
                                    placeholder="NIK 16 digit..."
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
                                    placeholder="Telepon..."
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
                                <InputLabel htmlFor="edit_role" value="Role" />
                                <select
                                    id="edit_role"
                                    value={editForm.data.role}
                                    onChange={(e) => {
                                        editForm.setData(data => ({
                                            ...data,
                                            role: e.target.value,
                                            supervisor_id: e.target.value !== 'support' ? '' : data.supervisor_id
                                        }));
                                    }}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="manager">Manager</option>
                                    <option value="supervisor">Supervisor</option>
                                    <option value="support">Support</option>
                                </select>
                                <InputError message={editForm.errors.role} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_cabang" value="Cabang" />
                                <select
                                    id="edit_cabang"
                                    value={editForm.data.cabang}
                                    onChange={(e) => {
                                        editForm.setData(data => ({
                                            ...data,
                                            cabang: e.target.value,
                                            supervisor_id: ''
                                        }));
                                    }}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    {cabangs.map(c => (
                                        <option key={c} value={c}>{c}</option>
                                    ))}
                                </select>
                                <InputError message={editForm.errors.cabang} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="edit_hire_date" value="Hire Date" />
                                <TextInput
                                    id="edit_hire_date"
                                    type="date"
                                    className="mt-1 block w-full"
                                    value={editForm.data.hire_date}
                                    onChange={(e) => editForm.setData('hire_date', e.target.value)}
                                    required
                                />
                                <InputError message={editForm.errors.hire_date} className="mt-2" />
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

                            {editForm.data.role === 'support' && (
                                <div className="md:col-span-2">
                                    <InputLabel htmlFor="edit_supervisor_id" value="Supervisor (Cabang yang sama)" />
                                    <select
                                        id="edit_supervisor_id"
                                        value={editForm.data.supervisor_id}
                                        onChange={(e) => editForm.setData('supervisor_id', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        required
                                    >
                                        <option value="">Pilih Supervisor</option>
                                        {getFilteredSupervisors(editForm.data.cabang).map(s => (
                                            <option key={s.id} value={s.id}>{s.nama}</option>
                                        ))}
                                    </select>
                                    {getFilteredSupervisors(editForm.data.cabang).length === 0 && (
                                        <p className="mt-1 text-xs text-rose-500">Tidak ada supervisor aktif di cabang {editForm.data.cabang}.</p>
                                    )}
                                    <InputError message={editForm.errors.supervisor_id} className="mt-2" />
                                </div>
                            )}

                            <div>
                                <InputLabel htmlFor="edit_is_active" value="Status Aktif" />
                                <select
                                    id="edit_is_active"
                                    value={editForm.data.is_active}
                                    onChange={(e) => editForm.setData('is_active', parseInt(e.target.value))}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                >
                                    <option value={1}>Aktif</option>
                                    <option value={0}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => { setIsEditOpen(false); setSelectedUser(null); }}
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
            <Modal show={isDeleteOpen} onClose={() => { setIsDeleteOpen(false); setSelectedUser(null); }}>
                <div className="p-6">
                    <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Hapus Akun User</h3>
                    <p className="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus akun user <span className="font-bold text-gray-900 dark:text-gray-100">{selectedUser?.nama}</span>? Tindakan ini akan menghapus seluruh data dan hak aksesnya.
                    </p>

                    <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button
                            type="button"
                            onClick={() => { setIsDeleteOpen(false); setSelectedUser(null); }}
                            className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleDeleteConfirm}
                            className="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                        >
                            Ya, Hapus Akun
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
