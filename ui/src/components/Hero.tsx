import { motion } from "motion/react";
import { Download, PlayCircle, Sparkles } from "lucide-react";

export function Hero() {
  return (
    <header className="relative pt-32 pb-20 px-6 hero-gradient overflow-hidden">
      <div className="max-w-7xl mx-auto text-center space-y-6 relative z-10">
        <motion.div 
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          className="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel border-primary/20 text-primary text-[10px] font-bold tracking-widest mb-4"
        >
          <Sparkles className="w-3 h-3" />
          V2.4 NOW AVAILABLE
        </motion.div>
        <motion.h1 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="text-5xl md:text-7xl font-semibold leading-tight max-w-4xl mx-auto bg-clip-text text-transparent bg-gradient-to-b from-white to-white/60 tracking-tight"
        >
          Love shooting. Make the first edit feel light again.
        </motion.h1>
        <motion.p 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.2 }}
          className="text-lg md:text-xl text-on-surface-variant max-w-2xl mx-auto leading-relaxed font-medium"
        >
          Assist sits beside DaVinci Resolve and builds your first timeline — dialogue cuts, music montages, reel clones, and beat-synced edits — while you keep creative control.
        </motion.p>
        <motion.div 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.3 }}
          className="flex flex-col sm:flex-row items-center justify-center gap-4 pt-8"
        >
          <motion.button 
            whileHover={{ y: -2, scale: 1.01 }}
            className="w-full sm:w-auto px-10 py-5 bg-primary text-white rounded-xl text-sm font-bold shadow-2xl shadow-primary/20 flex items-center justify-center gap-3 active:scale-95 transition-all uppercase tracking-widest"
          >
            <Download className="w-5 h-5" />
            Download for Mac
          </motion.button>
          <motion.button 
            whileHover={{ backgroundColor: "rgba(255, 255, 255, 0.05)" }}
            className="w-full sm:w-auto px-10 py-5 bg-transparent border border-white/20 text-on-surface rounded-xl text-sm font-bold flex items-center justify-center gap-3 transition-all uppercase tracking-widest"
          >
            <PlayCircle className="w-5 h-5" />
            See how it works
          </motion.button>
        </motion.div>
      </div>

      <motion.div 
        initial={{ opacity: 0, scale: 0.98, y: 40 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        transition={{ delay: 0.4, duration: 0.8 }}
        className="mt-20 max-w-6xl mx-auto relative group"
      >
        <div className="absolute -inset-4 bg-gradient-to-r from-primary/10 via-secondary/10 to-primary/10 rounded-[2rem] blur-3xl opacity-20 group-hover:opacity-30 transition-all"></div>
        <div className="glass-panel p-3 rounded-3xl relative overflow-hidden border-white/10 shadow-3xl">
          <img 
            alt="Editor Interface" 
            referrerPolicy="no-referrer"
            className="rounded-2xl w-full" 
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQw2ppT0RaRpV-C2RPi8_3Ox6d1Pw1y5QMvocjHax5VzkGLbM12UpUF7MDQqFUIX6XrI0ULafBzXpQTeLFlqSefdQxzwFD8Y7XSgymW3roxQ1nHCpH81DsCw8jFz0O30pbQse_Cc-vfw0nmMkKzMN1DDu4kk59ofUHGkgYNAgJK5Jwc-n7a61IrBE9tSY3n5VrdGDDdhl683zBgCfyvAetTN9mZtLuEwokGO33HIM8T0p8pWGRsHaBXEmq85dvaqPrbtFMm6ReHA"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-surface/40 to-transparent pointer-events-none"></div>
        </div>
      </motion.div>
    </header>
  );
}
