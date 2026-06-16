import Dropdown from '@/Components/Dropdown';
import { Link, usePage } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { 
    LayoutDashboard, 
    Building2, 
    Users, 
    UserSquare2, 
    FileText, 
    GitCompare, 
    ClipboardList, 
    LogOut, 
    User, 
    Menu, 
    X,
    Sun,
    Moon,
    BookOpen
} from 'lucide-react';

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [darkMode, setDarkMode] = useState(() => {
        const saved = localStorage.getItem('theme');
        if (saved) return saved === 'dark';
        return document.documentElement.classList.contains('dark');
    });

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
    };

    useEffect(() => {
        if (darkMode) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }, [darkMode]);

    // Helper to check user roles
    const hasRole = (roles) => {
        if (!user.role) return false;
        return roles.includes(user.role.toLowerCase());
    };

    // Define sidebar menu items based on roles
    const menuItems = [
        {
            label: 'Dashboard',
            icon: LayoutDashboard,
            href: route('dashboard'),
            active: route().current('dashboard'),
            roles: ['admin', 'manager', 'supervisor', 'support', 'mitra']
        },
        {
            label: 'Cabang',
            icon: Building2,
            href: route('cabang.index'),
            active: route().current('cabang.*'),
            roles: ['admin']
        },
        {
            label: 'Leads',
            icon: FileText,
            href: route('leads.index'),
            active: route().current('leads.*'),
            roles: ['admin', 'manager', 'supervisor', 'support', 'mitra']
        },
        {
            label: 'Mitra',
            icon: UserSquare2,
            href: route('mitra.index'),
            active: route().current('mitra.*'),
            roles: ['admin', 'manager', 'supervisor', 'support']
        },
        {
            label: 'Users',
            icon: Users,
            href: route('users.index'),
            active: route().current('users.*'),
            roles: ['admin']
        },
        {
            label: 'Upline Requests',
            icon: GitCompare,
            href: route('upline-requests.index'),
            active: route().current('upline-requests.*'),
            roles: ['manager', 'supervisor']
        },
        {
            label: 'Audit Logs',
            icon: ClipboardList,
            href: route('audit-logs.index'),
            active: route().current('audit-logs.*'),
            roles: ['admin', 'manager']
        },
        {
            label: 'Documentation',
            icon: BookOpen,
            href: route('documentation.index'),
            active: route().current('documentation.*'),
            roles: ['admin', 'manager', 'supervisor']
        }
    ];

    const filteredMenu = menuItems.filter(item => hasRole(item.roles));

    return (
        <div className="min-h-screen bg-gray-50 text-gray-800 transition-colors duration-300 dark:bg-gray-950 dark:text-gray-100">
            
            {/* Mobile Header */}
            <div className="flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900 lg:hidden">
                <div className="flex items-center gap-3">
                    <img 
                        src="/images/icons8-magnet-96.png" 
                        alt="Lead Magnet Logo" 
                        className="h-8 w-8 object-contain" 
                    />
                    <span className="font-bold text-lg tracking-wide bg-gradient-to-r from-brand-accent to-brand-primary bg-clip-text text-transparent">
                        Lead Magnet
                    </span>
                </div>
                <div className="flex items-center gap-4">
                    <button
                        onClick={toggleDarkMode}
                        className="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                    >
                        {darkMode ? <Sun size={20} /> : <Moon size={20} />}
                    </button>
                    <button
                        onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                        className="rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                    >
                        {isSidebarOpen ? <X size={24} /> : <Menu size={24} />}
                    </button>
                </div>
            </div>

            {/* Backdrop for Mobile Sidebar */}
            {isSidebarOpen && (
                <div 
                    onClick={() => setIsSidebarOpen(false)}
                    className="fixed inset-0 z-40 bg-gray-950/40 backdrop-blur-sm lg:hidden"
                />
            )}

            {/* Sidebar */}
            <aside className={`fixed bottom-0 top-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-300 dark:border-gray-800 dark:bg-gray-900 lg:translate-x-0 ${isSidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                {/* Brand Logo */}
                <div className="flex h-16 items-center gap-3 px-6 border-b border-gray-200 dark:border-gray-800">
                    <img 
                        src="/images/icons8-magnet-96.png" 
                        alt="Lead Magnet Logo" 
                        className="h-9 w-9 object-contain" 
                    />
                    <span className="font-extrabold text-xl tracking-wider bg-gradient-to-r from-brand-accent to-brand-primary bg-clip-text text-transparent">
                        Lead Magnet
                    </span>
                </div>

                {/* Logged In User Info */}
                <div className="flex flex-col items-center border-b border-gray-200 px-6 py-5 text-center dark:border-gray-800">
                    <div className="relative mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-tr from-brand-accent via-brand-primary to-brand-accent p-[2px] shadow-lg">
                        <div className="flex h-full w-full items-center justify-center rounded-full bg-white dark:bg-gray-900">
                            <span className="text-xl font-bold bg-gradient-to-tr from-brand-accent to-brand-primary bg-clip-text text-transparent">
                                {user.nama ? user.nama.substring(0, 2).toUpperCase() : 'US'}
                            </span>
                        </div>
                    </div>
                    <span className="font-bold text-sm text-gray-900 dark:text-gray-50">{user.nama}</span>
                    <span className="mt-1 inline-flex items-center rounded-full bg-brand-accent/10 px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider text-brand-primary border border-brand-accent/20">
                        {user.role}
                    </span>
                    <span className="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Cabang: {user.cabang || 'Pusat'}</span>
                </div>

                {/* Navigation Menu */}
                <nav className="flex-1 space-y-1 px-4 py-4 overflow-y-auto">
                    {filteredMenu.map((item, index) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={index}
                                href={item.href}
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 ${item.active 
                                    ? 'bg-gradient-to-r from-brand-accent to-brand-primary text-white shadow-md shadow-brand-primary/10' 
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100'}`}
                            >
                                <Icon size={18} className={item.active ? 'text-white' : 'text-gray-500 group-hover:text-gray-900 dark:text-gray-400'} />
                                <span>{item.label}</span>
                            </Link>
                        );
                    })}
                </nav>

                {/* Sidebar Footer */}
                <div className="border-t border-gray-200 p-4 dark:border-gray-800 flex items-center justify-between">
                    <button
                        onClick={toggleDarkMode}
                        className="hidden lg:flex rounded-xl p-2.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                    >
                        {darkMode ? <Sun size={18} /> : <Moon size={18} />}
                    </button>

                    <div className="flex items-center gap-2">
                        <Link
                            href={route('profile.edit')}
                            className="rounded-xl p-2.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-100"
                            title="Profile"
                        >
                            <User size={18} />
                        </Link>
                        
                        <Link
                            method="post"
                            href={route('logout')}
                            as="button"
                            className="rounded-xl p-2.5 text-rose-500 hover:bg-rose-50/50 hover:text-rose-600 dark:hover:bg-rose-950/20"
                            title="Sign Out"
                        >
                            <LogOut size={18} />
                        </Link>
                    </div>
                </div>
            </aside>

            {/* Main Content Area */}
            <div className="lg:pl-64 flex flex-col min-h-screen">
                {/* Header */}
                {header && (
                    <header className="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 sticky top-0 z-30 hidden lg:block px-8 py-5">
                        <div className="flex items-center justify-between">
                            {header}
                            
                            {/* Flash Success Message Display in Top Corner if needed */}
                            <div className="text-xs text-gray-500 dark:text-gray-400">
                                Logged in as: <span className="font-semibold text-gray-900 dark:text-gray-100">{user.email}</span>
                            </div>
                        </div>
                    </header>
                )}

                {/* Page Content */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8">
                    {children}
                </main>
            </div>
        </div>
    );
}
