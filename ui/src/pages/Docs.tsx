import { motion } from "motion/react";
import { Search, ChevronRight, BookOpen, Terminal, Code2, HelpCircle, Zap } from "lucide-react";

const sections = [
  {
    title: "Getting Started",
    icon: <BookOpen className="w-5 h-5" />,
    items: ["Installation", "Connecting Resolve", "First Project", "UI Overview"]
  },
  {
    title: "Core Features",
    icon: <Zap className="w-5 h-5" />,
    items: ["AI Editor Engine", "Reels Cloner", "Beat Edit", "Transcriptions"]
  },
  {
    title: "Workflows",
    icon: <Terminal className="w-5 h-5" />,
    items: ["Wedding Highlights", "Documentary Assembly", "Social Ads", "Podcast Cuts"]
  },
  {
    title: "Advanced",
    icon: <Code2 className="w-5 h-5" />,
    items: ["Story Graph", ".assistproject Format", "Custom Templates", "API Access"]
  },
  {
    title: "Support",
    icon: <HelpCircle className="w-5 h-5" />,
    items: ["FAQ", "Troubleshooting", "System Requirements", "Contact Us"]
  }
];

export function Docs() {
  return (
    <div className="pt-32 pb-20 px-6 max-w-7xl mx-auto flex flex-col md:flex-row gap-12">
      {/* Sidebar */}
      <aside className="w-full md:w-64 shrink-0 space-y-8">
        <div className="relative group">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-on-surface-variant" />
          <input 
            type="text" 
            placeholder="Search docs..."
            className="w-full bg-surface-container border border-white/10 rounded-xl py-3 pl-10 pr-4 text-xs font-medium focus:border-primary/50 outline-none transition-all"
          />
        </div>

        <nav className="space-y-6">
          {sections.map((section) => (
            <div key={section.title} className="space-y-3">
              <div className="flex items-center gap-2 text-on-surface font-bold text-[10px] uppercase tracking-[0.1em] opacity-60">
                {section.icon}
                {section.title}
              </div>
              <ul className="space-y-2 border-l border-white/5 ml-2.5 pl-4">
                {section.items.map((item) => (
                  <li key={item}>
                    <a href="#" className="text-xs font-medium text-on-surface-variant hover:text-primary transition-colors block py-0.5">
                      {item}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </nav>
      </aside>

      {/* Content */}
      <main className="flex-1 space-y-12">
        <header className="space-y-4">
          <div className="flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest">
            Docs <ChevronRight className="w-3 h-3" /> Getting Started
          </div>
          <h1 className="text-4xl font-semibold tracking-tight">Installation Guide</h1>
          <p className="text-on-surface-variant text-lg font-medium leading-relaxed max-w-3xl">
            Learn how to set up Assist and connect it with DaVinci Resolve for a seamless post-production workflow.
          </p>
        </header>

        <article className="prose prose-invert max-w-none space-y-8">
          <section className="space-y-4">
            <h2 className="text-2xl font-semibold tracking-tight border-b border-white/5 pb-2">1. Download and Install</h2>
            <p className="text-on-surface-variant font-medium leading-relaxed">
              Download the latest version of Assist for macOS from our downloads page. Drag the application to your <code>/Applications</code> folder and launch it.
            </p>
            <div className="bg-surface-container-low p-6 rounded-2xl border border-white/5 font-mono text-xs text-primary/80">
              $ unzip Assist_v2.4_macOS.zip<br/>
              $ mv Assist.app /Applications
            </div>
          </section>

          <section className="space-y-4">
            <h2 className="text-2xl font-semibold tracking-tight border-b border-white/5 pb-2">2. Enable Resolve Scripting</h2>
            <p className="text-on-surface-variant font-medium leading-relaxed">
              Assist requires external scripting to be enabled in DaVinci Resolve to build timelines and manage bins.
            </p>
            <ul className="list-disc list-inside space-y-2 text-on-surface-variant font-medium pl-4">
              <li>Open DaVinci Resolve Preferences (Cmd + ,)</li>
              <li>Go to <strong>System &gt; General</strong></li>
              <li>Set <strong>External scripting using</strong> to <code>Local</code></li>
              <li>Restart DaVinci Resolve</li>
            </ul>
          </section>

          <section className="space-y-4">
            <h2 className="text-2xl font-semibold tracking-tight border-b border-white/5 pb-2">3. First Connection</h2>
            <p className="text-on-surface-variant font-medium leading-relaxed">
              Once both apps are running, Assist will automatically detect the active project in Resolve. A green connection indicator will appear in the Assist sidebar.
            </p>
          </section>
        </article>

        <div className="pt-12 border-t border-white/5 flex justify-between items-center">
          <div className="text-on-surface-variant text-[11px] font-medium italic">
            Last updated: May 19, 2026
          </div>
          <button className="flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity">
            Next: Connecting Resolve <ChevronRight className="w-3 h-3" />
          </button>
        </div>
      </main>
    </div>
  );
}
