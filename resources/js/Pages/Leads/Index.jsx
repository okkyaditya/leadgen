import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Table from '@/Components/Table';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, useForm, router, usePage } from '@inertiajs/react';
import { Edit2, Trash2, X, Download } from 'lucide-react';
import { useState } from 'react';

export default function Index({ leads, cabangs, products, mitras, filters }) {
    const currentUser = usePage().props.auth.user;
    const [isCreateOpen, setIsCreateOpen] = useState(false);
    const [isEditOpen, setIsEditOpen] = useState(false);
    const [selectedLead, setSelectedLead] = useState(null);
    const [isDeleteOpen, setIsDeleteOpen] = useState(false);

    // Initial state matching Lead form fields
    const initialFormState = {
        nama: '',
        telepon: '',
        nik: '',
        produk: products[0] || 'NDF Car',
        tipe_lead: 'Tanya-tanya',
        ntf: '',
        unit: '',
        no_unit: '',
        domisili: '',
        source_mitra_id: '',
    };

    const createForm = useForm(initialFormState);
    const editForm = useForm({ ...initialFormState, id: null });

    const handleCreateSubmit = (e) => {
        e.preventDefault();
        createForm.post(route('leads.store'), {
            onSuccess: () => {
                createForm.reset();
                setIsCreateOpen(false);
            }
        });
    };

    const handleEditClick = (lead) => {
        setSelectedLead(lead);
        editForm.setData({
            nama: lead.nama || '',
            telepon: lead.telepon || '',
            nik: lead.nik || '',
            produk: lead.produk || 'NDF Car',
            tipe_lead: lead.tipe_lead || 'Tanya-tanya',
            ntf: lead.ntf || '',
            unit: lead.unit || '',
            no_unit: lead.no_unit || '',
            domisili: lead.domisili || '',
            source_mitra_id: lead.source_mitra_id || '',
        });
        setIsEditOpen(true);
    };

    const handleEditSubmit = (e) => {
        e.preventDefault();
        editForm.put(route('leads.update', selectedLead.id), {
            onSuccess: () => {
                setIsEditOpen(false);
                setSelectedLead(null);
            }
        });
    };

    const handleDeleteClick = (lead) => {
        setSelectedLead(lead);
        setIsDeleteOpen(true);
    };

    const handleDeleteConfirm = () => {
        router.delete(route('leads.destroy', selectedLead.id), {
            onSuccess: () => {
                setIsDeleteOpen(false);
                setSelectedLead(null);
            }
        });
    };

    const handleSearch = (searchVal) => {
        router.get(route('leads.index'), { 
            search: searchVal,
            cabang: filters.cabang || '',
            produk: filters.produk || ''
        }, { preserveState: true });
    };

    const handleFilterChange = (key, value) => {
        router.get(route('leads.index'), {
            ...filters,
            [key]: value
        }, { preserveState: true });
    };

    const handleExport = () => {
        // Build export query string based on current active filters
        const params = new URLSearchParams();
        if (filters.search) params.append('search', filters.search);
        if (filters.cabang) params.append('cabang', filters.cabang);
        if (filters.produk) params.append('produk', filters.produk);
        
        window.open(route('leads.export') + '?' + params.toString());
    };

    // Format currency (IDR)
    const formatCurrency = (value) => {
        if (!value) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    };

    // Columns Definition
    const columns = [
        { label: 'Nama Lengkap', field: 'nama' },
        { label: 'Telepon', field: 'telepon' },
        { label: 'Produk', field: 'produk' },
        { 
            label: 'Status Leads', 
            field: 'tipe_lead',
            render: (row) => {
                const statusColors = {
                    'Tanya-tanya': 'bg-slate-500/10 text-slate-500 border border-slate-500/20',
                    'Thinking': 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
                    'Negotiation': 'bg-amber-500/10 text-amber-500 border border-amber-500/20',
                    'Cancel': 'bg-gray-500/10 text-gray-500 border border-gray-500/20',
                    'Lose deal': 'bg-rose-500/10 text-rose-500 border border-rose-500/20',
                    'Survey': 'bg-purple-500/10 text-purple-500 border border-purple-500/20',
                    'Reject': 'bg-red-500/10 text-red-500 border border-red-500/20',
                    'Funding': 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20'
                };
                const statusClass = statusColors[row.tipe_lead] || 'bg-slate-500/10 text-slate-500 border border-slate-500/20';
                return (
                    <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider ${statusClass}`}>
                        {row.tipe_lead || 'Tanya-tanya'}
                    </span>
                );
            }
        },
        { 
            label: 'Nilai NTF', 
            field: 'ntf',
            render: (row) => formatCurrency(row.ntf)
        },
        { 
            label: 'Sumber Mitra', 
            field: 'source_mitra',
            render: (row) => row.source_mitra?.nama || '-'
        },
        { 
            label: 'Input Oleh', 
            field: 'input_by',
            render: (row) => (
                <div>
                    <span className="font-bold text-gray-900 dark:text-gray-100">{row.input_by?.nama}</span>
                    <span className="block text-[10px] text-gray-500 uppercase">{row.input_by?.role}</span>
                </div>
            )
        },
        { label: 'Domisili', field: 'domisili' },
        { 
            label: 'Tanggal Input', 
            field: 'created_at',
            render: (row) => new Date(row.created_at).toLocaleDateString('id-ID')
        }
    ];

    // Filter controls for admin
    const isAdmin = currentUser.role === 'admin';
    const filterControls = (
        <div className="flex flex-wrap sm:flex-nowrap gap-2 w-full sm:w-auto">
            {isAdmin && (
                <select
                    value={filters.cabang || ''}
                    onChange={(e) => handleFilterChange('cabang', e.target.value)}
                    className="flex-1 sm:flex-none rounded-2xl border-gray-200 bg-gray-50 text-sm focus:border-brand-accent focus:bg-white focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-800 dark:focus:border-brand-accent dark:focus:bg-gray-900 transition-all py-2.5 pl-4 pr-10"
                >
                    <option value="">Semua Cabang</option>
                    {cabangs.map(c => (
                        <option key={c} value={c}>{c}</option>
                    ))}
                </select>
            )}

            <select
                value={filters.produk || ''}
                onChange={(e) => handleFilterChange('produk', e.target.value)}
                className="flex-1 sm:flex-none rounded-2xl border-gray-200 bg-gray-50 text-sm focus:border-brand-accent focus:bg-white focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-800 dark:focus:border-brand-accent dark:focus:bg-gray-900 transition-all py-2.5 pl-4 pr-10"
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

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50">
                    Manajemen Leads
                </h2>
            }
        >
            <Head title="Manajemen Leads" />

            <div className="space-y-6">
                <Table
                    title="Daftar Prospek (Leads)"
                    description="Kelola, filter, dan unduh data prospek penjualan di bawah naungan Anda."
                    columns={columns}
                    data={leads}
                    onSearch={handleSearch}
                    searchValue={filters.search || ''}
                    onAdd={() => setIsCreateOpen(true)}
                    addLabel="Tambah Lead"
                    filters={filterControls}
                    emptyMessage="Lead tidak ditemukan."
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
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Tambah Lead Baru</h3>
                        <button onClick={() => setIsCreateOpen(false)} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleCreateSubmit} className="mt-4 flex flex-col">
                        <div className="max-h-[60vh] overflow-y-auto px-1 space-y-4 pb-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="nama" value="Nama Lengkap" />
                                    <TextInput
                                        id="nama"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={createForm.data.nama}
                                        onChange={(e) => createForm.setData('nama', e.target.value)}
                                        placeholder="Nama Lengkap"
                                        required
                                    />
                                    <InputError message={createForm.errors.nama} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="nik" value="No. KTP" />
                                    <TextInput
                                        id="nik"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={createForm.data.nik}
                                        onChange={(e) => createForm.setData('nik', e.target.value)}
                                        placeholder="1234567891234567"
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
                                        placeholder="08xxxxxxxxxx"
                                        maxLength={16}
                                        required
                                    />
                                    <InputError message={createForm.errors.telepon} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="produk" value="Produk" />
                                    <select
                                        id="produk"
                                        value={createForm.data.produk}
                                        onChange={(e) => createForm.setData('produk', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-accent focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    >
                                        <option value="" disabled>Pilih Produk</option>
                                        {products.map(p => (
                                            <option key={p} value={p}>{p}</option>
                                        ))}
                                    </select>
                                    <InputError message={createForm.errors.produk} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="no_unit" value="Nomor Unit" />
                                    <TextInput
                                        id="no_unit"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={createForm.data.no_unit}
                                        onChange={(e) => createForm.setData('no_unit', e.target.value)}
                                        placeholder="B xxxx ABC"
                                    />
                                    <InputError message={createForm.errors.no_unit} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="tipe_lead" value="Status Leads" />
                                    <select
                                        id="tipe_lead"
                                        value={createForm.data.tipe_lead}
                                        onChange={(e) => createForm.setData('tipe_lead', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-accent focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    >
                                        <option value="Tanya-tanya">Tanya-tanya</option>
                                        <option value="Thinking">Thinking</option>
                                        <option value="Negotiation">Negotiation</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Lose deal">Lose deal</option>
                                        <option value="Survey">Survey</option>
                                        <option value="Reject">Reject</option>
                                        <option value="Funding">Funding</option>
                                    </select>
                                    <InputError message={createForm.errors.tipe_lead} className="mt-2" />
                                </div>

                                <div className="md:col-span-2">
                                    <InputLabel htmlFor="owner" value="Pemilik Lead" />
                                    <TextInput
                                        id="owner"
                                        type="text"
                                        className="mt-1 block w-full bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed"
                                        value={currentUser.nama}
                                        disabled
                                        readOnly
                                    />
                                </div>
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
                                Simpan Lead
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Modal */}
            <Modal show={isEditOpen} onClose={() => { setIsEditOpen(false); setSelectedLead(null); }} maxWidth="2xl">
                <div className="p-6">
                    <div className="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                        <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Perbarui Detail Lead</h3>
                        <button onClick={() => { setIsEditOpen(false); setSelectedLead(null); }} className="text-gray-400 hover:text-gray-500">
                            <X size={18} />
                        </button>
                    </div>

                    <form onSubmit={handleEditSubmit} className="mt-4 flex flex-col">
                        <div className="max-h-[60vh] overflow-y-auto px-1 space-y-4 pb-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="edit_nama" value="Nama Lengkap" />
                                    <TextInput
                                        id="edit_nama"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={editForm.data.nama}
                                        onChange={(e) => editForm.setData('nama', e.target.value)}
                                        placeholder="Nama Lengkap"
                                        required
                                    />
                                    <InputError message={editForm.errors.nama} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="edit_nik" value="No. KTP" />
                                    <TextInput
                                        id="edit_nik"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={editForm.data.nik}
                                        onChange={(e) => editForm.setData('nik', e.target.value)}
                                        placeholder="1234567891234567"
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
                                        placeholder="08xxxxxxxxxx"
                                        maxLength={16}
                                        required
                                    />
                                    <InputError message={editForm.errors.telepon} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="edit_produk" value="Produk" />
                                    <select
                                        id="edit_produk"
                                        value={editForm.data.produk}
                                        onChange={(e) => editForm.setData('produk', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-accent focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    >
                                        {products.map(p => (
                                            <option key={p} value={p}>{p}</option>
                                        ))}
                                    </select>
                                    <InputError message={editForm.errors.produk} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="edit_no_unit" value="Nomor Unit" />
                                    <TextInput
                                        id="edit_no_unit"
                                        type="text"
                                        className="mt-1 block w-full"
                                        value={editForm.data.no_unit}
                                        onChange={(e) => editForm.setData('no_unit', e.target.value)}
                                        placeholder="B xxxx ABC"
                                    />
                                    <InputError message={editForm.errors.no_unit} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel htmlFor="edit_tipe_lead" value="Status Leads" />
                                    <select
                                        id="edit_tipe_lead"
                                        value={editForm.data.tipe_lead}
                                        onChange={(e) => editForm.setData('tipe_lead', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-accent focus:ring-brand-accent dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                    >
                                        <option value="Tanya-tanya">Tanya-tanya</option>
                                        <option value="Thinking">Thinking</option>
                                        <option value="Negotiation">Negotiation</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Lose deal">Lose deal</option>
                                        <option value="Survey">Survey</option>
                                        <option value="Reject">Reject</option>
                                        <option value="Funding">Funding</option>
                                    </select>
                                    <InputError message={editForm.errors.tipe_lead} className="mt-2" />
                                </div>

                                <div className="md:col-span-2">
                                    <InputLabel htmlFor="edit_owner" value="Pemilik Lead" />
                                    <TextInput
                                        id="edit_owner"
                                        type="text"
                                        className="mt-1 block w-full bg-gray-100 dark:bg-gray-800 text-gray-500 cursor-not-allowed"
                                        value={selectedLead?.input_by?.nama || currentUser.nama}
                                        disabled
                                        readOnly
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                            <button
                                type="button"
                                onClick={() => { setIsEditOpen(false); setSelectedLead(null); }}
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
            <Modal show={isDeleteOpen} onClose={() => { setIsDeleteOpen(false); setSelectedLead(null); }}>
                <div className="p-6">
                    <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">Hapus Data Lead</h3>
                    <p className="mt-2 text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus data prospek <span className="font-bold text-gray-900 dark:text-gray-100">{selectedLead?.nama}</span>? Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <div className="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                        <button
                            type="button"
                            onClick={() => { setIsDeleteOpen(false); setSelectedLead(null); }}
                            className="rounded-2xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleDeleteConfirm}
                            className="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-rose-700 active:scale-95 transition-all shadow-md shadow-brand-primary/10"
                        >
                            Ya, Hapus Data
                        </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}
