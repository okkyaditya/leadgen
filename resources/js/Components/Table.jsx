import { Link } from '@inertiajs/react';
import { Search, ChevronDown, Plus, ChevronLeft, ChevronRight } from 'lucide-react';
import { useState } from 'react';

export default function Table({ 
    title, 
    description, 
    columns, 
    data, 
    onSearch, 
    searchValue = '', 
    onAdd, 
    addLabel = 'Tambah Baru',
    filters,
    children,
    emptyMessage = 'Belum ada data tersedia.'
}) {
    const [search, setSearch] = useState(searchValue);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        if (onSearch) {
            onSearch(search);
        }
    };

    return (
        <div className="rounded-3xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            {/* Table Header Controls */}
            <div className="flex flex-col gap-4 border-b border-gray-200 p-6 dark:border-gray-800 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 className="text-lg font-black text-gray-900 dark:text-gray-50">{title}</h3>
                    {description && <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">{description}</p>}
                </div>
                
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center lg:justify-end">
                    {/* Search form */}
                    {onSearch && (
                        <form onSubmit={handleSearchSubmit} className="relative flex-1 sm:w-64">
                            <input
                                type="text"
                                placeholder="Cari data..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                className="w-full rounded-2xl border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:border-amber-500 focus:bg-white focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:border-amber-500 dark:focus:bg-gray-900 dark:focus:ring-amber-500 transition-all"
                            />
                            <Search className="absolute left-3 top-3 h-4 w-4 text-gray-400" />
                        </form>
                    )}

                    {/* Extra filter slots */}
                    {filters}

                    {/* Add button */}
                    {onAdd && (
                        <button
                            onClick={onAdd}
                            className="flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-brand-accent to-brand-primary px-4 py-2.5 text-sm font-bold text-white shadow-md shadow-brand-primary/10 hover:opacity-90 hover:shadow-lg active:scale-95 transition-all"
                        >
                            <Plus size={16} />
                            <span>{addLabel}</span>
                        </button>
                    )}
                </div>
            </div>

            {/* Table Content */}
            <div className="overflow-x-auto">
                <table className="w-full border-collapse text-left text-sm text-gray-500 dark:text-gray-400">
                    <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <tr>
                            {columns.map((col, idx) => (
                                <th 
                                    key={idx} 
                                    className={`px-6 py-4 font-semibold ${col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'}`}
                                    style={{ width: col.width }}
                                >
                                    {col.label}
                                </th>
                            ))}
                            {children && <th className="px-6 py-4 text-right font-semibold">Aksi</th>}
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-100 border-t border-gray-100 dark:divide-gray-800 dark:border-gray-800">
                        {data.data && data.data.length > 0 ? (
                            data.data.map((row, rowIdx) => (
                                <tr key={rowIdx} className="hover:bg-gray-50/50 dark:hover:bg-gray-800/20 transition-colors">
                                    {columns.map((col, colIdx) => (
                                        <td 
                                            key={colIdx} 
                                            className={`px-6 py-4 text-gray-900 dark:text-gray-100 font-medium ${col.align === 'right' ? 'text-right' : col.align === 'center' ? 'text-center' : 'text-left'}`}
                                        >
                                            {col.render ? col.render(row) : row[col.field]}
                                        </td>
                                    ))}
                                    {children && (
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex justify-end gap-2">
                                                {children(row)}
                                            </div>
                                        </td>
                                    )}
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan={columns.length + (children ? 1 : 0)} className="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div className="flex flex-col items-center justify-center gap-2">
                                        <Search size={32} className="text-gray-300 dark:text-gray-700" />
                                        <span className="text-sm font-semibold">{emptyMessage}</span>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Table Pagination */}
            {data.links && data.links.length > 3 && (
                <div className="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
                    <div className="flex-1 flex justify-between sm:hidden">
                        {data.prev_page_url ? (
                            <Link
                                href={data.prev_page_url}
                                className="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Previous
                            </Link>
                        ) : (
                            <span className="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-400 dark:border-gray-800 dark:bg-gray-800">
                                Previous
                            </span>
                        )}
                        {data.next_page_url ? (
                            <Link
                                href={data.next_page_url}
                                className="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"
                            >
                                Next
                            </Link>
                        ) : (
                            <span className="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-400 dark:border-gray-800 dark:bg-gray-800">
                                Next
                            </span>
                        )}
                    </div>
                    <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p className="text-xs text-gray-500 dark:text-gray-400">
                                Showing <span className="font-semibold text-gray-900 dark:text-gray-100">{data.from ?? 0}</span> to <span className="font-semibold text-gray-900 dark:text-gray-100">{data.to ?? 0}</span> of <span className="font-semibold text-gray-900 dark:text-gray-100">{data.total}</span> entries
                            </p>
                        </div>
                        <div>
                            <nav className="relative z-0 inline-flex -space-x-px rounded-xl shadow-sm" aria-label="Pagination">
                                {data.links.map((link, idx) => {
                                    if (idx === 0) {
                                        return link.url ? (
                                            <Link
                                                key={idx}
                                                href={link.url}
                                                className="relative inline-flex items-center rounded-l-xl border border-gray-200 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                            >
                                                <ChevronLeft size={16} />
                                            </Link>
                                        ) : (
                                            <span key={idx} className="relative inline-flex items-center rounded-l-xl border border-gray-100 bg-gray-50 px-2 py-2 text-sm font-medium text-gray-300 dark:border-gray-800 dark:bg-gray-800">
                                                <ChevronLeft size={16} />
                                            </span>
                                        );
                                    }
                                    if (idx === data.links.length - 1) {
                                        return link.url ? (
                                            <Link
                                                key={idx}
                                                href={link.url}
                                                className="relative inline-flex items-center rounded-r-xl border border-gray-200 bg-white px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
                                            >
                                                <ChevronRight size={16} />
                                            </Link>
                                        ) : (
                                            <span key={idx} className="relative inline-flex items-center rounded-r-xl border border-gray-100 bg-gray-50 px-2 py-2 text-sm font-medium text-gray-300 dark:border-gray-800 dark:bg-gray-800">
                                                <ChevronRight size={16} />
                                            </span>
                                        );
                                    }
                                    return (
                                        <Link
                                            key={idx}
                                            href={link.url ?? '#'}
                                            className={`relative inline-flex items-center border border-gray-200 px-3.5 py-2 text-xs font-bold transition-all ${link.active 
                                                ? 'z-10 bg-amber-500 border-amber-500 text-white shadow-sm shadow-amber-500/15' 
                                                : 'bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'}`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    );
                                })}
                            </nav>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
