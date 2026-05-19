import { motion } from "motion/react";
import { Copy, Music, UploadCloud, ChevronDown } from "lucide-react";

export function Features() {
  return (
    <section className="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-7xl mx-auto px-6 py-20">
      {/* Reels Cloner */}
      <motion.div 
        whileHover={{ y: -4 }}
        className="glass-panel p-10 rounded-[2.5rem] relative overflow-hidden group border-white/5"
      >
        <div className="new-badge absolute top-8 right-8 px-4 py-1 rounded-full text-white text-[10px] font-bold tracking-widest uppercase">NEW</div>
        <div className="space-y-4">
          <div className="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center">
            <Copy className="text-secondary w-6 h-6" />
          </div>
          <h3 className="text-3xl font-semibold tracking-tight">Reels Cloner</h3>
          <p className="text-on-surface-variant text-sm leading-relaxed font-medium">
            Drop a reference reel and Assist will analyze the rhythm, pacing, and color science to match your footage perfectly to the vibe.
          </p>
        </div>
        <div className="mt-10 h-56 bg-surface-container-low/40 rounded-[2rem] border border-white/5 flex flex-col items-center justify-center relative hover:bg-surface-container-low/60 transition-colors cursor-pointer group-hover:border-secondary/20">
          <div className="absolute inset-0 opacity-10 bg-gradient-to-b from-secondary to-transparent"></div>
          <UploadCloud className="w-8 h-8 text-on-surface-variant mb-4 group-hover:text-secondary group-hover:scale-110 transition-all" />
          <span className="text-[10px] text-on-surface-variant font-bold uppercase tracking-widest">Drop MP4 or Link here</span>
        </div>
      </motion.div>

      {/* Beat Edit */}
      <motion.div 
        whileHover={{ y: -4 }}
        className="glass-panel p-10 rounded-[2.5rem] relative overflow-hidden group border-white/5"
      >
        <div className="new-badge absolute top-8 right-8 px-4 py-1 rounded-full text-white text-[10px] font-bold tracking-widest uppercase">NEW</div>
        <div className="space-y-4">
          <div className="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center">
            <Music className="text-primary w-6 h-6" />
          </div>
          <h3 className="text-3xl font-semibold tracking-tight">Beat Edit</h3>
          <p className="text-on-surface-variant text-sm leading-relaxed font-medium">
            Automated music-sync montage creation. Our engine detects transients and harmonic shifts to place cuts with surgical precision.
          </p>
        </div>
        <div className="mt-10 flex items-end gap-2 h-56 px-6">
          {[20, 60, 90, 40, 70, 30, 85, 40].map((height, i) => (
            <motion.div 
              key={i}
              initial={{ height: 0 }}
              animate={{ height: `${height}%` }}
              transition={{ delay: i * 0.1, duration: 1, repeat: Infinity, repeatType: "reverse" }}
              className={`flex-1 ${height > 80 ? 'bg-primary' : 'bg-primary/30'} rounded-t-xl relative`}
            >
              {height > 80 && (
                <div className="absolute -top-6 left-1/2 -translate-x-1/2 text-primary animate-bounce">
                  <ChevronDown className="w-4 h-4" />
                </div>
              )}
            </motion.div>
          ))}
        </div>
      </motion.div>
    </section>
  );
}
