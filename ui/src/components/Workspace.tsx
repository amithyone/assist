import { useState } from "react";
import { motion, AnimatePresence } from "motion/react";
import { FileText, LayoutGrid, Palette, ListTodo, Clock, Brain } from "lucide-react";

const tabs = [
  { id: 'plan', label: 'Plan' },
  { id: 'execution', label: 'Execution' },
  { id: 'story', label: 'Story Graph' },
  { id: 'ai', label: 'AI Treatment' }
];

export function Workspace() {
  const [activeTab, setActiveTab] = useState('plan');

  return (
    <section className="space-y-12 py-20 px-6 max-w-7xl mx-auto">
      <div className="text-center space-y-4 max-w-2xl mx-auto">
        <span className="text-[10px] uppercase tracking-[0.2em] text-primary font-bold block">Preparation</span>
        <h2 className="text-4xl font-semibold tracking-tight">Preproduction Workspace</h2>
        <p className="text-on-surface-variant text-lg font-medium">
          Where stories are born before the first frame is captured. A unified ecosystem for your creative blueprints.
        </p>
      </div>

      <div className="glass-panel rounded-[2.5rem] overflow-hidden border-white/5 bg-surface-container-low/20">
        <div className="flex border-b border-white/5 bg-surface-container-low/40 overflow-x-auto no-scrollbar">
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex-1 min-w-[150px] py-6 text-[10px] uppercase font-bold tracking-[0.2em] transition-all relative ${
                activeTab === tab.id ? 'text-primary' : 'text-on-surface-variant hover:text-on-surface'
              }`}
            >
              {tab.label}
              {activeTab === tab.id && (
                <motion.div 
                  layoutId="activeTab"
                  className="absolute bottom-0 left-0 right-0 h-1 bg-primary" 
                />
              )}
            </button>
          ))}
        </div>

        <div className="p-10 min-h-[500px]">
          <AnimatePresence mode="wait">
            {activeTab === 'plan' && (
              <motion.div 
                key="plan"
                initial={{ opacity: 0, x: 20 }}
                animate={{ opacity: 1, x: 0 }}
                exit={{ opacity: 0, x: -20 }}
                className="grid grid-cols-1 md:grid-cols-4 gap-8"
              >
                <div className="space-y-4">
                  <div className="flex items-center gap-2 text-primary font-bold uppercase text-[10px] tracking-widest">
                    <FileText className="w-4 h-4" />
                    <span>Creative Brief</span>
                  </div>
                  <div className="bg-surface-container p-6 rounded-2xl h-44 border border-white/5 text-on-surface-variant text-sm italic leading-relaxed overflow-hidden font-medium">
                    "A cinematic exploration of urban loneliness versus digital connectivity. Use deep shadows and neon flares to emphasize contrast..."
                  </div>
                </div>
                <div className="space-y-4">
                  <div className="flex items-center gap-2 text-primary font-bold uppercase text-[10px] tracking-widest">
                    <LayoutGrid className="w-4 h-4" />
                    <span>Storyboard</span>
                  </div>
                  <div className="bg-surface-container rounded-2xl h-44 overflow-hidden border border-white/5 group cursor-pointer relative">
                    <img 
                      alt="Storyboard" 
                      referrerPolicy="no-referrer"
                      className="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-all duration-700" 
                      src="https://lh3.googleusercontent.com/aida-public/AB6AXuAfwo6qbDqeu68k3BGoD7peScNY8Mx-G1bP0W_oOgcOmilzjLIZjJgYHdQGxqts72YCDeT0cZLrmWeT8qYZq6MRSDAKWBeS6jzm3Z95rYyR1np_BLl-4luVDM30cdAqnNCnOATcKaveeuwjl0wzasgrdICngiQqw9FyUPLvPd8TZ1SYXvsF4S8jizjm_FVSzqB_Lqoe-7V6pAz48hi8mGvNtfblbWPc52wIT9C1UVGa91VPin30CNQslrvwJv_F3Ujv64uucruLiQ"
                    />
                    <div className="absolute inset-0 bg-primary/10 opacity-0 group-hover:opacity-100 transition-opacity" />
                  </div>
                </div>
                <div className="space-y-4">
                  <div className="flex items-center gap-2 text-primary font-bold uppercase text-[10px] tracking-widest">
                    <Palette className="w-4 h-4" />
                    <span>Moodboard</span>
                  </div>
                  <div className="flex flex-wrap gap-2 pt-1">
                    <div className="w-14 h-14 bg-[#000965] rounded-xl shadow-lg hover:scale-110 transition-all cursor-crosshair m-0.5" />
                    <div className="w-14 h-14 bg-[#5e6ad2] rounded-xl shadow-lg hover:scale-110 transition-all cursor-crosshair m-0.5" />
                    <div className="w-14 h-14 bg-[#9d50bb] rounded-xl shadow-lg hover:scale-110 transition-all cursor-crosshair m-0.5" />
                    <div className="w-14 h-14 bg-[#131315] rounded-xl border border-white/10 shadow-lg hover:scale-110 transition-all cursor-crosshair m-0.5" />
                  </div>
                  <div className="text-[10px] text-on-surface-variant font-bold uppercase tracking-widest mt-2 bg-white/5 py-1 px-2 rounded-full inline-block">Atmospheric / Cyber / Noir</div>
                </div>
                <div className="space-y-4">
                  <div className="flex items-center gap-2 text-primary font-bold uppercase text-[10px] tracking-widest">
                    <ListTodo className="w-4 h-4" />
                    <span>Shot List</span>
                  </div>
                  <div className="space-y-2">
                    {[
                      { id: '01', label: 'CU Eyes Tracking', tech: '35mm' },
                      { id: '02', label: 'Wide Drone Est.', tech: '24mm' },
                      { id: '03', label: 'Profile Shallow', tech: '85mm' },
                    ].map((shot) => (
                      <div key={shot.id} className="p-3 bg-surface-container-high/40 rounded-xl text-[11px] flex justify-between border border-white/5 hover:border-primary/20 transition-colors">
                        <span className="font-semibold text-on-surface">{shot.id}. {shot.label}</span>
                        <span className="text-secondary font-bold uppercase tracking-widest text-[9px]">{shot.tech}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </motion.div>
            )}

            {activeTab === 'story' && (
              <motion.div 
                key="story"
                initial={{ opacity: 0, scale: 0.98 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.98 }}
                className="relative h-[450px] flex items-center justify-center overflow-hidden"
              >
                <div className="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--color-primary)_0%,transparent_70%)]" />
                <svg className="absolute inset-0 w-full h-full pointer-events-none opacity-20">
                  <motion.path 
                    d="M100 100 Q 300 50, 400 200 T 700 350"
                    fill="none"
                    stroke="url(#gradient)"
                    strokeWidth="2"
                    strokeDasharray="8 4"
                    initial={{ pathLength: 0 }}
                    animate={{ pathLength: 1 }}
                    transition={{ duration: 2 }}
                  />
                  <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                      <stop offset="0%" stopColor="#5e6ad2" />
                      <stop offset="100%" stopColor="#9d50bb" />
                    </linearGradient>
                  </defs>
                </svg>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-6 relative z-10 w-full max-w-5xl">
                   <motion.div whileHover={{ y: -4 }} className="glass-panel p-5 rounded-2xl border-primary/20 ai-glow">
                      <div className="text-[10px] text-primary font-bold uppercase mb-2 tracking-widest">ACT I</div>
                      <div className="text-sm font-semibold">The Invitation</div>
                   </motion.div>
                   <motion.div whileHover={{ y: -4 }} className="glass-panel p-5 rounded-2xl border-secondary/20 translate-y-24">
                      <div className="text-[10px] text-secondary font-bold uppercase mb-2 tracking-widest">INCITING INCIDENT</div>
                      <div className="text-sm font-semibold">The Discovery</div>
                   </motion.div>
                   <motion.div whileHover={{ y: -4 }} className="glass-panel p-5 rounded-2xl border-primary/20 -translate-y-8">
                      <div className="text-[10px] text-primary font-bold uppercase mb-2 tracking-widest">MIDPOINT</div>
                      <div className="text-sm font-semibold">The Conflict Escalates</div>
                   </motion.div>
                   <motion.div whileHover={{ y: -4 }} className="glass-panel p-5 rounded-2xl border-tertiary/20 translate-y-12">
                      <div className="text-[10px] text-tertiary font-bold uppercase mb-2 tracking-widest">CLIMAX</div>
                      <div className="text-sm font-semibold">Final Confrontation</div>
                   </motion.div>
                </div>
              </motion.div>
            )}

            {(activeTab === 'execution' || activeTab === 'ai') && (
              <motion.div 
                key="placeholder"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0 }}
                className="flex flex-col items-center justify-center py-20 space-y-8"
              >
                <div className="w-24 h-24 bg-surface-container/60 rounded-[2rem] flex items-center justify-center text-on-surface-variant border border-white/5 shadow-2xl">
                  {activeTab === 'execution' ? <Clock className="w-10 h-10" /> : <Brain className="w-10 h-10 text-secondary" />}
                </div>
                <div className="text-center space-y-3">
                  <h4 className="text-2xl font-semibold tracking-tight">
                    {activeTab === 'execution' ? 'Execution Checklist Module' : 'Production Intelligence'}
                  </h4>
                  <p className="text-on-surface-variant max-w-md mx-auto text-sm leading-relaxed font-medium">
                    {activeTab === 'execution' 
                      ? 'Automated wrap reports, gear inventory tracking, and real-time shoot day progress sync with your entire production crew.'
                      : 'Generate treatment options and shot ideas based on your core creative brief using our specialized cinematic models.'}
                  </p>
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </div>
      </div>
    </section>
  );
}
