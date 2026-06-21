import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { 
    ArrowRight, 
    Sparkles, 
    Heart, 
    Loader2 
} from 'lucide-react';

export default function Welcome({ auth }) {
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const timer = setTimeout(() => {
            setLoading(false);
        }, 1200); // 1.2s premium loading screen
        return () => clearTimeout(timer);
    }, []);

    if (loading) {
        return (
            <div 
                className="fixed inset-0 z-50 flex flex-col items-center justify-center text-slate-900 select-none"
                style={{ 
                    backgroundImage: "url('/images/login-bg.jpg')", 
                    backgroundSize: 'cover', 
                    backgroundPosition: 'center',
                    backgroundRepeat: 'no-repeat'
                }}
            >
                <div className="relative flex flex-col items-center gap-4">
                    {/* Animated glowing ring */}
                    <div className="absolute h-20 w-20 animate-ping rounded-full bg-brand-accent/20" />
                    
                    <img 
                        src="/images/icons8-magnet-96.png" 
                        alt="Leads Tracker Logo" 
                        className="h-16 w-16 object-contain animate-bounce" 
                    />
                    
                    <h2 className="mt-4 text-xl font-black tracking-widest bg-gradient-to-r from-brand-accent to-brand-primary bg-clip-text text-transparent">
                        LEADS TRACKER
                    </h2>
                    
                    <div className="flex items-center gap-2 text-xs text-slate-500 font-semibold mt-2">
                        <Loader2 className="animate-spin text-brand-primary" size={14} />
                        <span>Next-Gen Platform Loading...</span>
                    </div>
                </div>
            </div>
        );
    }

    // Animation Variants
    const fadeUp = {
        hidden: { opacity: 0, y: 30 },
        visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } }
    };

    const staggerContainer = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.15
            }
        }
    };

    return (
        <div 
            className="min-h-screen text-slate-900 selection:bg-brand-primary selection:text-white dark:text-slate-50 relative overflow-hidden flex flex-col justify-between"
            style={{ 
                backgroundImage: "url('/images/login-bg.jpg')", 
                backgroundSize: 'cover', 
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat'
            }}
        >
            <Head title="Welcome to Leads Tracker">
                <meta name="description" content="Leads Tracker - Next-Gen platform to maximize sales, track leads, and accelerate commercial growth. Collaborate seamlessly with your sales team." />
                <meta name="keywords" content="leads tracker, sales tracker, lead management, sales pipeline, commercial growth" />
                <meta property="og:title" content="Leads Tracker - Next-Gen Analytics Platform" />
                <meta property="og:description" content="Maximize sales, track leads, and grow faster. Collaborate seamlessly with your sales team." />
                <meta property="og:image" content="/images/icons8-magnet-96.png" />
                <meta property="og:type" content="website" />
                <meta property="twitter:card" content="summary" />
                <meta property="twitter:title" content="Leads Tracker - Next-Gen Analytics Platform" />
                <meta property="twitter:description" content="Maximize sales, track leads, and grow faster. Collaborate seamlessly with your sales team." />
                <meta property="twitter:image" content="/images/icons8-magnet-96.png" />
            </Head>

            {/* Main Hero Section */}
            <main className="relative z-10 w-full max-w-7xl mx-auto px-6 py-12 flex-1 flex flex-col items-center justify-center text-center">
                
                <motion.div 
                    variants={staggerContainer}
                    initial="hidden"
                    animate="visible"
                    className="flex flex-col items-center justify-center w-full"
                >
                    {/* Centered Logo and Brand Name */}
                    <motion.div variants={fadeUp} className="flex flex-col items-center gap-3 mb-8 select-none">
                        <img 
                            src="/images/icons8-magnet-96.png" 
                            alt="Leads Tracker Logo" 
                            className="h-16 w-16 object-contain" 
                        />
                        <span className="font-extrabold text-2xl sm:text-3xl tracking-wider bg-gradient-to-r from-brand-accent to-brand-primary bg-clip-text text-transparent">
                            Leads Tracker
                        </span>
                    </motion.div>

                    {/* Hero Badge */}
                    <motion.div variants={fadeUp} className="inline-flex items-center gap-1.5 rounded-full bg-brand-accent/10 border border-brand-accent/20 px-4 py-1.5 text-xs font-bold text-brand-primary dark:text-brand-accent select-none animate-bounce mb-6">
                        <Sparkles size={12} />
                        <span>Next-Gen Analytics Platform</span>
                    </motion.div>

                    {/* Hero Title */}
                    <motion.h1 variants={fadeUp} className="text-4xl sm:text-6xl font-black tracking-tight leading-[1.1] max-w-4xl text-slate-900 dark:text-white">
                        Maximize Sales, <span className="bg-gradient-to-r from-brand-accent to-brand-primary bg-clip-text text-transparent">Track Leads</span>,<br />
                        Grow Faster.
                    </motion.h1>

                    {/* Hero Subtitle */}
                    <motion.p variants={fadeUp} className="mt-6 text-sm sm:text-base text-slate-500 dark:text-slate-400 max-w-2xl font-medium leading-relaxed">
                        Leads Tracker empowers your team to collaborate seamlessly, capture high-quality leads, and accelerate commercial growth in one united platform.
                    </motion.p>

                    {/* CTA Section */}
                    <motion.div variants={fadeUp} className="mt-10 flex flex-row items-center justify-center gap-4">
                        <Link 
                            href={route('login')}
                            className="w-44 flex items-center justify-center rounded-full bg-slate-900 py-3.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200"
                        >
                            Get Started
                        </Link>
                        <Link 
                            href={route('login')}
                            className="w-44 flex items-center justify-center rounded-full bg-white/60 backdrop-blur-md py-3.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200/60 hover:bg-white transition-all dark:bg-slate-900/60 dark:text-white dark:ring-slate-800/60 dark:hover:bg-slate-900"
                        >
                            Login
                        </Link>
                    </motion.div>
                </motion.div>
            </main>

            {/* Footer */}
            <motion.footer 
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                transition={{ duration: 1, delay: 0.8 }}
                className="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 border-t border-slate-200/50 dark:border-slate-800/50 text-center text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <p>&copy; {new Date().getFullYear()} Leads Tracker. All rights reserved.</p>
                <p className="flex items-center gap-1">
                    <span>Made with</span>
                    <Heart size={12} className="text-rose-500 fill-rose-500" />
                    <span>for high performance sales teams.</span>
                </p>
            </motion.footer>

        </div>
    );
}
