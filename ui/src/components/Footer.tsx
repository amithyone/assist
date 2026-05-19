import { Video, Globe, ArrowRight } from "lucide-react";

export function Footer() {
  return (
    <footer className="w-full pt-32 pb-16 bg-surface-container-lowest border-t border-white/5">
      <div className="max-w-7xl mx-auto px-6">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-12">
          {/* Brand Section */}
          <div className="md:col-span-4 space-y-6">
            <div className="flex items-center gap-3">
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
                <Video className="text-white w-5 h-5" />
              </div>
              <div className="text-xl font-bold text-on-surface tracking-tight">Assist</div>
            </div>
            <p className="text-on-surface-variant text-sm max-w-xs leading-relaxed font-medium">
              The professional standard for video post-production. From first assembly to final export.
            </p>
            <div className="flex gap-4">
              <a className="w-10 h-10 rounded-xl glass-panel flex items-center justify-center hover:text-primary transition-all hover:scale-110" href="#">
                <Globe className="w-5 h-5" />
              </a>
              <a className="w-10 h-10 rounded-xl glass-panel flex items-center justify-center hover:text-primary transition-all hover:scale-110" href="#">
                <Video className="w-5 h-5" />
              </a>
            </div>
          </div>

          {/* Links Sections */}
          <div className="md:col-span-2 space-y-6">
            <h5 className="text-on-surface text-[10px] font-bold uppercase tracking-widest">Product</h5>
            <ul className="space-y-4 text-on-surface-variant text-sm font-medium">
              <li><a className="hover:text-primary transition-colors" href="#">Features</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Pricing</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Download</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Release Notes</a></li>
            </ul>
          </div>
          <div className="md:col-span-2 space-y-6">
            <h5 className="text-on-surface text-[10px] font-bold uppercase tracking-widest">Company</h5>
            <ul className="space-y-4 text-on-surface-variant text-sm font-medium">
              <li><a className="hover:text-primary transition-colors" href="#">About</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Blog</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Careers</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Contact</a></li>
            </ul>
          </div>
          <div className="md:col-span-2 space-y-6">
            <h5 className="text-on-surface text-[10px] font-bold uppercase tracking-widest">Support</h5>
            <ul className="space-y-4 text-on-surface-variant text-sm font-medium">
              <li><a className="hover:text-primary transition-colors" href="#">Documentation</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Help Center</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Community</a></li>
              <li><a className="hover:text-primary transition-colors" href="#">Status</a></li>
            </ul>
          </div>

          {/* Newsletter */}
          <div className="md:col-span-2 space-y-6">
            <h5 className="text-on-surface text-[10px] font-bold uppercase tracking-widest">Updates</h5>
            <div className="relative group">
              <input 
                className="w-full bg-surface-container border border-white/10 rounded-xl py-3 px-4 text-xs focus:border-primary/50 outline-none transition-all group-hover:border-white/20 font-medium" 
                placeholder="Email address" 
                type="email" 
              />
              <button className="absolute right-1 top-1 bottom-1 px-3 bg-primary text-white rounded-lg hover:brightness-110 transition-all">
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <div className="mt-20 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-6">
          <div className="text-on-surface-variant text-[11px] font-medium opacity-60">
            © 2024 Assist Editor. All rights reserved. Built by Amithy Innocent.
          </div>
          <div className="flex gap-8">
            <a className="text-on-surface-variant hover:text-primary transition-colors text-[10px] font-bold uppercase tracking-widest" href="#">Privacy</a>
            <a className="text-on-surface-variant hover:text-primary transition-colors text-[10px] font-bold uppercase tracking-widest" href="#">Terms</a>
            <a className="text-on-surface-variant hover:text-primary transition-colors text-[10px] font-bold uppercase tracking-widest" href="#">Cookies</a>
          </div>
        </div>
      </div>
    </footer>
  );
}
