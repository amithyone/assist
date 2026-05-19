import { motion } from "motion/react";
import { Languages, Film, Zap, Info } from "lucide-react";

export function EditingEngine() {
  return (
    <section className="max-w-5xl mx-auto space-y-10 py-32 px-6 text-center">
      <header className="space-y-4">
        <span className="text-[10px] uppercase tracking-[0.2em] text-primary font-bold block">Intelligent Workflow</span>
        <h2 className="text-4xl md:text-5xl font-semibold tracking-tight">Powerful Editing Engine.</h2>
        <p className="text-lg text-on-surface-variant max-w-2xl mx-auto font-medium">
          Assist reads your raw rushes, transcribes clips, and crafts a narrative-first timeline directly in your preferred NLE.
        </p>
      </header>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-8 text-left items-center pt-8">
        <div className="space-y-8">
          <div className="space-y-4">
            <label className="text-[10px] uppercase text-on-surface-variant font-bold tracking-widest">Active Intelligence</label>
            <div className="space-y-3">
              <motion.div 
                whileHover={{ x: 4 }}
                className="flex items-center gap-4 p-4 bg-surface-container rounded-xl border border-white/5 group transition-colors hover:border-primary/20"
              >
                <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                  <Languages className="text-primary w-5 h-5" />
                </div>
                <div>
                  <div className="text-sm font-semibold text-on-surface">Auto-Transcription</div>
                  <div className="text-xs text-on-surface-variant">99% accuracy across 40+ languages.</div>
                </div>
              </motion.div>
              <motion.div 
                whileHover={{ x: 4 }}
                className="flex items-center gap-4 p-4 bg-surface-container/40 rounded-xl border border-white/5 group transition-colors hover:border-secondary/20"
              >
                <div className="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center">
                  <Film className="text-secondary w-5 h-5" />
                </div>
                <div>
                  <div className="text-sm font-semibold text-on-surface">Smart Scene Detection</div>
                  <div className="text-xs text-on-surface-variant">Categorizes footage by visual style and sentiment.</div>
                </div>
              </motion.div>
            </div>
          </div>
          <motion.button 
            whileHover={{ scale: 0.99, y: -1 }}
            whileTap={{ scale: 0.97 }}
            className="w-full bg-primary text-white py-5 rounded-xl text-sm font-bold shadow-xl shadow-primary/10 flex items-center justify-center gap-3 transition-all uppercase tracking-widest"
          >
            <Zap className="w-4 h-4" />
            Execute First Assembly
          </motion.button>
        </div>

        <div className="glass-panel p-8 rounded-2xl border-white/5 space-y-6">
          <div className="flex justify-between items-center">
            <span className="text-[10px] font-bold text-primary tracking-widest uppercase">PROJECT TYPE</span>
            <Info className="w-4 h-4 text-on-surface-variant" />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <button className="p-4 bg-primary/10 border border-primary/40 text-primary rounded-lg text-center text-[11px] font-bold uppercase tracking-wider hover:bg-primary/20 transition-all">Wedding Cinema</button>
            <button className="p-4 bg-surface-container-high/40 border border-white/10 text-on-surface-variant rounded-lg text-center text-[11px] font-bold uppercase tracking-wider hover:bg-surface-container-high hover:text-on-surface transition-all">Documentary</button>
            <button className="p-4 bg-surface-container-high/40 border border-white/10 text-on-surface-variant rounded-lg text-center text-[11px] font-bold uppercase tracking-wider hover:bg-surface-container-high hover:text-on-surface transition-all">Commercial</button>
            <button className="p-4 bg-surface-container-high/40 border border-white/10 text-on-surface-variant rounded-lg text-center text-[11px] font-bold uppercase tracking-wider hover:bg-surface-container-high hover:text-on-surface transition-all">Social Content</button>
          </div>
        </div>
      </div>
    </section>
  );
}
