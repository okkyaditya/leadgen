import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, useForm, router } from '@inertiajs/react';
import { Edit2, Trash2, X } from 'lucide-react';
import { useState } from 'react';

export default function Index({ cabangs, filters }) {
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [selectedCabang, setSelectedCabang] = useState(null);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);

    // Create Form
    const createForm = useForm({
        nama: '',
    });

    // Edit Form
    const editForm = useForm({
        nama: '',
    });

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('cabang.store'), {
            onSuccess: () => {
                createForm.reset();
                setIsCreateOpen(false);
            }
        });
    };

    const handleEditClick = (cabang) => {
        setSelectedCabang(cabang);
        editForm.setData({ nama: cabang.nama });
        setIsEditOpen(true);
    };

    const handleEditSubmit = (e) => {
        e.preventDefault();
        editForm.put(route('cabang.update', selectedCabang.id), {
            onSuccess: () => {
                setIsEditOpen(false);
                setSelectedCabang(null);
            }
        });
    };

    const handleDeleteClick = (cabang) => {
        setSelectedCabang(cabang);
        setIsDeleteOpen(true);
    };

    const handleDeleteConfirm = () => {
        router.delete(route('cabang.destroy', selectedCabang.id), {
            onSuccess: () => {
                setIsDeleteOpen(false);
                setSelectedCabang(null);
            }
        });
    };

    // Columns Definition
    const columns = [
        { label: 'ID', field: 'id', width: '80px' },
        { label: 'Nama Cabang', field: 'nama' },
    ];

    const handleSearch = (searchVal) => {
        router.get(route('cabang.index'), { search: searchVal }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Manajemen Cabang
                </h2>
            }
        >
            <Head title="Manajemen Cabang" />

            <div className="space-y-6">
                <Table
                    title="Daftar Cabang"
                    description="Kelola seluruh cabang operasional yang terdaftar dalam sistem."
                    columns={columns}
                    data={cabangs}
                    onSearch={handleSearch}
                    searchValue={filters.search || ''}
                    onAdd={() => setIsCreateOpen(true)}
                    addLabel="Tambah Cabang"
                    emptyMessage="Cabang tidak ditemukan."
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
            <Modal show={isCreateOpen} onClose={() => setIsCreateOpen(false)}>
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Tambah Cabang Baru</h3>
                        <button onClick={() => setIsCreateOpen(false)} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleCreateSubmit} className="mt-4 space-y-4">
                        <div>
                            <InputLabel htmlFor="nama" value="Nama Cabang" />
                            <TextInput
                                id="nama"
                                type="text"
                                className="mt-1 block w-full"
                                value={createForm.data.nama}
                                onChange={(e) => createForm.setData('nama', e.target.value)}
                                placeholder="Masukkan nama cabang baru..."
                                required
                                isFocused
                            />
                            <InputError message={createForm.errors.nama} className="mt-2" />
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
                                Simpan Cabang
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Modal */}
            <Modal show={isEditOpen} onClose={() => { setIsEditOpen(false); setSelectedCabang(null); }}>
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Perbarui Cabang</h3>
                        <button onClick={() => { setIsEditOpen(false); setSelectedCabang(null); }} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleEditSubmit} className="mt-4 space-y-4">
                        <div>
                            <InputLabel htmlFor="edit_nama" value="Nama Cabang" />
                            <TextInput
                                id="edit_nama"
                                type="text"
                                className="mt-1 block w-full"
                                value={editForm.data.nama}
                                onChange={(e) => editForm.setData('nama', e.target.value)}
                                placeholder="Masukkan nama cabang..."
                                required
                                isFocused
                            />
                            <InputError message={editForm.errors.nama} className="mt-2" />
                        </div>

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => { setIsEditOpen(false); setSelectedCabang(null); }}
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
            <Modal show={isDeleteOpen} onClose={() => { setIsDeleteOpen(false); setSelectedCabang(null); }}>
                <div className="p-6">
                    <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Hapus Cabang</h3>
                    <p className="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus cabang <span className="font-bold text-gray-900 dark:text-gray-100">{selectedCabang?.nama}</span>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button
                            type="button"
                            onClick={() => { setIsDeleteOpen(false); setSelectedCabang(null); }}
                            className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleDeleteConfirm}
                            className="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                        >
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
