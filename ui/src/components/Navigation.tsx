import { motion } from "motion/react";
import { Video } from "lucide-react";
import { Link, useLocation } from "react-router-dom";

export function Navigation() {
  const location = useLocation();

  return (
    <nav className="fixed top-0 w-full z-50 bg-sidebar/60 backdrop-blur-xl border-b border-white/5 px-6 h-16 flex justify-between items-center group">
      <Link to="/" className="flex items-center gap-4 hover:opacity-80 transition-opacity">
        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-lg shadow-primary/20">
          <Video className="text-white w-5 h-5" />
        </div>
        <div className="text-lg font-semibold text-on-surface tracking-tight">Assist</div>
      </Link>
      <div className="hidden md:flex items-center gap-8">
        <Link 
          to="/"
          className={`transition-colors text-xs font-bold uppercase tracking-widest ${location.pathname === '/' ? 'text-primary' : 'text-on-surface-variant hover:text-on-surface'}`}
        >
          Features
        </Link>
        <Link 
          to="/pricing"
          className={`transition-colors text-xs font-bold uppercase tracking-widest ${location.pathname === '/pricing' ? 'text-primary' : 'text-on-surface-variant hover:text-on-surface'}`}
        >
          Pricing
        </Link>
        <a className="text-on-surface-variant hover:text-on-surface transition-colors text-xs font-bold uppercase tracking-widest" href="#">Download</a>
        <Link 
          to="/docs"
          className={`transition-colors text-xs font-bold uppercase tracking-widest ${location.pathname === '/docs' ? 'text-primary' : 'text-on-surface-variant hover:text-on-surface'}`}
        >
          Docs
        </Link>
      </div>
      <div className="flex items-center gap-4">
        <motion.button 
          whileHover={{ scale: 0.98, y: -1 }}
          whileTap={{ scale: 0.95 }}
          className="bg-primary hover:bg-primary-hover text-white px-6 py-2 rounded-lg text-xs font-bold shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all uppercase tracking-widest"
        >
          Get Started
        </motion.button>
      </div>
    </nav>
  );
}
