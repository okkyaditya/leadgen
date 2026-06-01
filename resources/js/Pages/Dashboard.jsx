import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, router } from '@inertiajs/react';
import { 
    Users2, 
    CheckCircle, 
    ShieldAlert, 
    Coins, 
    TrendingUp, 
    Award, 
    Calendar,
    ArrowUpRight,
    FileText
} from 'lucide-react';
import { useState } from 'react';

export default function Dashboard({ stats, chart, topMitras, topSupports = [] }) {
    const user = usePage().props.auth.user;
    const isAdmin = user.role === 'admin';
    const [selectedYear, setSelectedYear] = useState(chart.year);
    const [hoveredBarIndex, setHoveredBarIndex] = useState(null);
    const [leaderboardType, setLeaderboardType] = useState('mitra'); // 'mitra' or 'support'

    const handleYearChange = (year) => {
        setSelectedYear(year);
        router.get(route('dashboard'), { year: year }, { preserveState: true, preserveScroll: true });
    };

    const leaderboardData = leaderboardType === 'mitra' ? topMitras : topSupports;

    // Format currency (IDR)
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(value);
    };

    // Calculate max value for chart scaling
    const maxChartValue = Math.max(...chart.data, 10); // avoid divide by zero, default to 10

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-2xl font-black tracking-tight text-gray-900 dark:text-gray-50 flex items-center gap-2">
                    Dashboard Overview
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="space-y-8 animate-fade-in">
                
                {/* Header Welcome banner */}
                <div className="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-accent to-brand-primary p-8 shadow-xl">
                    <div className="absolute right-0 top-0 h-64 w-64 -translate-y-12 translate-x-12 rounded-full bg-white/10 blur-2xl" />
                    <div className="absolute bottom-0 left-0 h-32 w-32 translate-y-12 -translate-x-12 rounded-full bg-rose-600/20 blur-xl" />
                    
                    <div className="relative z-10 max-w-xl">
                        <span className="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white backdrop-blur-md">
                            System Active
                        </span>
                        <h1 className="mt-4 text-3xl font-extrabold text-white sm:text-4xl tracking-tight">
                            Selamat Datang Kembali!
                        </h1>
                        <p className="mt-2 text-white/80 font-medium">
                            Pantau perolehan prospek baru, audit aktivitas tim, dan kelola jaringan kemitraan Anda di seluruh cabang.
                        </p>
                    </div>
                </div>

                {/* Stats Grid */}
                <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
                    {/* Stat Card 1 */}
                    <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Mitra</span>
                            <div className="rounded-xl bg-amber-500/10 p-2.5 text-amber-500 transition-colors group-hover:bg-amber-500 group-hover:text-white">
                                <Users2 size={20} />
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-3xl font-black tracking-tight">{stats.totalMitra}</span>
                            <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">Total aktif & non-aktif</span>
                        </div>
                    </div>

                    {/* Stat Card 2 */}
                    <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Mitra Aktif</span>
                            <div className="rounded-xl bg-emerald-500/10 p-2.5 text-emerald-500 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                                <CheckCircle size={20} />
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-3xl font-black tracking-tight text-emerald-500">{stats.mitraAktif}</span>
                            <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">Mitra sedang aktif kerja</span>
                        </div>
                    </div>

                    {/* Stat Card 3 */}
                    {isAdmin ? (
                        <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                            <div className="flex items-center justify-between">
                                <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Total User</span>
                                <div className="rounded-xl bg-blue-500/10 p-2.5 text-blue-500 transition-colors group-hover:bg-blue-500 group-hover:text-white">
                                    <ShieldAlert size={20} />
                                </div>
                            </div>
                            <div className="mt-4">
                                <span className="text-3xl font-black tracking-tight">{stats.totalUser}</span>
                                <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">Manager, Spv, Support</span>
                            </div>
                        </div>
                    ) : (
                        <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900">
                            <div className="flex items-center justify-between">
                                <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Total Leads</span>
                                <div className="rounded-xl bg-brand-accent/50/10 p-2.5 text-brand-primary transition-colors group-hover:bg-brand-accent/50 group-hover:text-white">
                                    <FileText size={20} />
                                </div>
                            </div>
                            <div className="mt-4">
                                <span className="text-3xl font-black tracking-tight text-brand-primary">{stats.totalLeads}</span>
                                <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {user.role === 'mitra' ? 'Total prospek Anda' : 'Prospek tim & jaringan'}
                                </span>
                            </div>
                        </div>
                    )}

                    {/* Stat Card 4 */}
                    <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 sm:col-span-2 lg:col-span-1">
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Potential NTF</span>
                            <div className="rounded-xl bg-rose-500/10 p-2.5 text-rose-500 transition-colors group-hover:bg-rose-500 group-hover:text-white">
                                <Coins size={20} />
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-2xl font-black tracking-tight text-rose-500 block truncate">{formatCurrency(stats.potentialNtf)}</span>
                            <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">Akumulasi NTF Leads</span>
                        </div>
                    </div>

                    {/* Stat Card 5 */}
                    <div className="group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:border-gray-800 dark:bg-gray-900 sm:col-span-2 lg:col-span-1">
                        <div className="flex items-center justify-between">
                            <span className="text-sm font-semibold text-gray-500 dark:text-gray-400">Ticket Size</span>
                            <div className="rounded-xl bg-violet-500/10 p-2.5 text-violet-500 transition-colors group-hover:bg-violet-500 group-hover:text-white">
                                <TrendingUp size={20} />
                            </div>
                        </div>
                        <div className="mt-4">
                            <span className="text-2xl font-black tracking-tight text-violet-500 block truncate">{formatCurrency(stats.ticketSize)}</span>
                            <span className="block mt-1 text-xs text-gray-500 dark:text-gray-400">Rata-rata NTF per Lead</span>
                        </div>
                    </div>
                </div>

                <div className="grid gap-8 lg:grid-cols-3">
                    
                    {/* Leads Chart */}
                    <div className="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-2">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-4 dark:border-gray-800">
                            <div>
                                <h3 className="text-lg font-extrabold text-gray-900 dark:text-gray-50 flex items-center gap-2">
                                    <Calendar size={18} className="text-rose-500" />
                                    Monthly Chart Total Leads
                                </h3>
                                <p className="text-xs text-gray-500 dark:text-gray-400">Jumlah perolehan leads per bulan sepanjang tahun {selectedYear}</p>
                            </div>
                            <div className="mt-3 sm:mt-0">
                                <select
                                    value={selectedYear}
                                    onChange={(e) => handleYearChange(parseInt(e.target.value))}
                                    className="rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold focus:border-amber-500 focus:ring-amber-500 transition-all py-1.5 px-3 pr-8 shadow-sm text-gray-700 dark:text-gray-300"
                                >
                                    {[2026, 2025, 2024, 2023, 2022].map((year) => (
                                        <option key={year} value={year}>{year}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Animated Custom Tailwind Bar Chart */}
                        <div className="mt-6 flex h-64 items-end justify-between gap-2 px-2 sm:px-6 relative">
                            {chart.data.map((count, index) => {
                                const percentHeight = (count / maxChartValue) * 100;
                                return (
                                    <div 
                                        key={index} 
                                        className="relative flex-1 flex flex-col items-center group h-full justify-end cursor-pointer"
                                        onMouseEnter={() => setHoveredBarIndex(index)}
                                        onMouseLeave={() => setHoveredBarIndex(null)}
                                    >
                                        {/* Hover Tooltip */}
                                        {hoveredBarIndex === index && (
                                            <div className="absolute top-0 z-20 mb-2 rounded-lg bg-gray-900 dark:bg-gray-50 px-2 py-1 text-[10px] font-bold text-white dark:text-gray-950 shadow-md transition-all animate-bounce">
                                                {count} Leads
                                            </div>
                                        )}
                                        {/* Bar */}
                                        <div 
                                            style={{ height: `${percentHeight}%` }}
                                            className={`w-full rounded-t-lg transition-all duration-700 ${hoveredBarIndex === index
                                                ? 'bg-gradient-to-t from-brand-accent to-brand-primary'
                                                : 'bg-amber-500/80 dark:bg-amber-500/60'}`}
                                        />
                                        {/* Label */}
                                        <span className="mt-2 text-[10px] sm:text-xs font-semibold text-gray-500 dark:text-gray-400">
                                            {chart.labels[index]}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Top Performers (Top Mitras / Top Supports) */}
                    <div className="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                        <div className="border-b border-gray-100 pb-4 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 className="text-lg font-extrabold text-gray-900 dark:text-gray-50 flex items-center gap-2">
                                    <Award size={18} className="text-amber-500" />
                                    {leaderboardType === 'mitra' ? 'Top Performers (Mitra)' : 'Top Performers (Support)'}
                                </h3>
                                <p className="text-xs text-gray-500 dark:text-gray-400">
                                    {leaderboardType === 'mitra' 
                                        ? 'Mitra kontributor leads terbanyak saat ini' 
                                        : 'Support kontributor leads terbanyak saat ini'}
                                </p>
                            </div>
                            
                            {user.role === 'supervisor' && topSupports.length > 0 && (
                                <div className="flex bg-gray-50 dark:bg-gray-800 rounded-xl p-1 border border-gray-200 dark:border-gray-700">
                                    <button
                                        onClick={() => setLeaderboardType('mitra')}
                                        className={`rounded-lg px-3 py-1.5 text-xs font-bold transition-all ${leaderboardType === 'mitra' 
                                            ? 'bg-amber-500 text-white shadow-md' 
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-750'}`}
                                    >
                                        Mitra
                                    </button>
                                    <button
                                        onClick={() => setLeaderboardType('support')}
                                        className={`rounded-lg px-3 py-1.5 text-xs font-bold transition-all ${leaderboardType === 'support' 
                                            ? 'bg-amber-500 text-white shadow-md' 
                                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-750'}`}
                                    >
                                        Support
                                    </button>
                                </div>
                            )}
                        </div>

                        <div className="mt-4 divide-y divide-gray-100 dark:divide-gray-800">
                            {leaderboardData.length > 0 ? (
                                leaderboardData.map((item, index) => (
                                    <div key={item.id} className="flex items-center justify-between py-4 group">
                                        <div className="flex items-center gap-3">
                                            <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-500 font-black text-sm group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                                #{index + 1}
                                            </div>
                                            <div>
                                                <span className="font-bold text-sm text-gray-900 dark:text-gray-50 block group-hover:text-amber-500 transition-colors">
                                                    {item.nama}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {item.cabang || 'Pusat'}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <span className="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-bold text-gray-800 dark:text-gray-200">
                                                {item.leads_count} Leads
                                            </span>
                                            <ArrowUpRight size={16} className="text-gray-400 group-hover:text-amber-500 transition-colors" />
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                                    Belum ada data kontribusi Leads.
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
