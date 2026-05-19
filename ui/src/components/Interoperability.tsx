import { motion } from "motion/react";
import { Clapperboard, Package, FileAudio, FileVideo, StickyNote } from "lucide-react";

export function Interoperability() {
  return (
    <section className="px-6 py-20 max-w-7xl mx-auto">
      <div className="glass-panel p-12 rounded-[3.5rem] bg-gradient-to-br from-surface-container-low to-surface-container-lowest border-white/5 flex flex-col md:flex-row items-center gap-16 relative overflow-hidden">
        <div className="absolute top-0 right-0 w-96 h-96 bg-primary/5 blur-[120px] -translate-y-1/2 translate-x-1/2" />
        
        <div className="flex-1 space-y-8 text-center md:text-left relative z-10">
          <header className="space-y-4">
            <span className="text-[10px] uppercase tracking-[0.2em] text-secondary font-bold block">Interoperability</span>
            <h2 className="text-4xl md:text-5xl font-semibold tracking-tight leading-tight">The .assistproject Package.</h2>
          </header>
          <p className="text-lg text-on-surface-variant leading-relaxed font-medium">
            A proprietary file format that encapsulates your entire preproduction plan, metadata, and proxy links. One file to rule the entire post-production pipeline.
          </p>
          
          <motion.div 
            whileHover={{ scale: 1.02, y: -2 }}
            className="p-6 bg-surface-container-highest/30 rounded-3xl border border-primary/20 flex items-center gap-6 group cursor-pointer hover:bg-primary/5 transition-all overflow-hidden relative shadow-2xl"
          >
            <div className="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity" />
            <div className="w-16 h-16 bg-primary rounded-2xl flex items-center justify-center shadow-xl relative z-10 group-hover:scale-105 transition-transform">
              <Clapperboard className="text-white w-8 h-8" />
            </div>
            <div className="text-left relative z-10">
              <div className="text-xl font-semibold text-on-surface group-hover:text-primary transition-colors">Bridge to Director</div>
              <p className="text-sm text-on-surface-variant font-medium">Instantly compile your preproduction graph into the edit engine.</p>
            </div>
          </motion.div>
        </div>

        <div className="flex-1 w-full flex justify-center relative scale-110 md:scale-100">
          <div className="relative w-72 h-72 md:w-96 md:h-96 glass-panel rounded-full flex items-center justify-center border-dashed border-2 border-white/10 group">
            <motion.div 
              animate={{ scale: [1, 1.1, 1], opacity: [0.05, 0.1, 0.05] }}
              transition={{ duration: 6, repeat: Infinity }}
              className="absolute inset-0 bg-primary rounded-full blur-3xl" 
            />
            
            <div className="w-3/4 h-3/4 bg-primary/5 rounded-full flex items-center justify-center relative border border-white/5 shadow-inner">
              <div className="w-1/2 h-1/2 bg-primary/10 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-700">
                <Package className="w-20 h-20 text-primary opacity-80" />
              </div>
            </div>

            {/* Floating Assets */}
            <motion.div 
              animate={{ y: [0, -12, 0], rotate: [12, 18, 12] }}
              transition={{ duration: 4, repeat: Infinity }}
              className="absolute -top-4 -right-2 p-5 glass-panel rounded-2xl shadow-3xl rotate-12 group-hover:translate-x-2 transition-transform"
            >
              <FileAudio className="text-secondary w-7 h-7" />
            </motion.div>
            <motion.div 
              animate={{ y: [0, 12, 0], rotate: [-12, -18, -12] }}
              transition={{ duration: 5, repeat: Infinity, delay: 0.5 }}
              className="absolute bottom-8 -left-10 p-5 glass-panel rounded-2xl shadow-3xl -rotate-12 group-hover:-translate-x-2 transition-transform"
            >
              <FileVideo className="text-primary w-7 h-7" />
            </motion.div>
            <motion.div 
              animate={{ x: [0, 12, 0] }}
              transition={{ duration: 4, repeat: Infinity, delay: 1 }}
              className="absolute top-1/2 -right-12 p-5 glass-panel rounded-2xl shadow-3xl group-hover:translate-x-4 transition-transform text-tertiary"
            >
              <StickyNote className="w-7 h-7" />
            </motion.div>
          </div>
        </div>
      </div>
    </section>
  );
}
