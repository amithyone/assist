import { motion } from "motion/react";
import { Check, Zap, Shield, Crown } from "lucide-react";

const plans = [
  {
    name: "Solo",
    price: "Free",
    description: "Perfect for independent creators starting out.",
    features: [
      "3 Timelines / month",
      "Standard Transcription",
      "Manual Beat Edit",
      "Basic Story Graph",
      "Community Support"
    ],
    highlight: false
  },
  {
    name: "Pro",
    price: "$29",
    period: "/mo",
    description: "For professional editors who need more power.",
    features: [
      "Unlimited Timelines",
      "Whisper High-Quality Transcription",
      "Full Reels Cloner",
      "Advanced AI Story Analysis",
      "Priority Email Support"
    ],
    highlight: true,
    badge: "Most Popular"
  },
  {
    name: "Studio",
    price: "$99",
    period: "/mo",
    description: "Enterprise features for production houses.",
    features: [
      "Multi-user Workspace",
      "External API Integrations",
      "Custom Project Templates",
      "Dedicated Success Manager",
      "Studio Mode (Local Host)"
    ],
    highlight: false
  }
];

export function Pricing() {
  return (
    <div className="pt-32 pb-20 px-6 max-w-7xl mx-auto">
      <div className="text-center space-y-4 mb-20">
        <motion.span 
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          className="text-[10px] uppercase tracking-[0.2em] text-primary font-bold block"
        >
          Pricing
        </motion.span>
        <motion.h1 
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
          className="text-4xl md:text-6xl font-semibold tracking-tight"
        >
          Simple, transparent plans.
        </motion.h1>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        {plans.map((plan, i) => (
          <motion.div
            key={plan.name}
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 + i * 0.1 }}
            className={`glass-panel p-8 rounded-[2rem] flex flex-col relative overflow-hidden group ${
              plan.highlight ? 'border-primary/40 ring-1 ring-primary/20 bg-primary/5' : 'border-white/5'
            }`}
          >
            {plan.badge && (
              <div className="absolute top-4 right-4 new-badge px-3 py-0.5 rounded-full text-[10px] font-bold text-white uppercase tracking-widest">
                {plan.badge}
              </div>
            )}
            
            <div className="mb-8">
              <h3 className="text-xl font-bold mb-2">{plan.name}</h3>
              <div className="flex items-baseline gap-1 mb-4">
                <span className="text-4xl font-bold">{plan.price}</span>
                {plan.period && <span className="text-on-surface-variant font-medium">{plan.period}</span>}
              </div>
              <p className="text-on-surface-variant text-sm font-medium leading-relaxed">
                {plan.description}
              </p>
            </div>

            <div className="space-y-4 mb-10 flex-1">
              {plan.features.map((feature) => (
                <div key={feature} className="flex items-start gap-3">
                  <div className={`mt-0.5 w-5 h-5 rounded-full flex items-center justify-center shrink-0 ${
                    plan.highlight ? 'bg-primary/20 text-primary' : 'bg-white/5 text-on-surface-variant'
                  }`}>
                    <Check className="w-3 h-3" />
                  </div>
                  <span className="text-sm font-medium text-on-surface">{feature}</span>
                </div>
              ))}
            </div>

            <motion.button
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              className={`w-full py-4 rounded-xl text-xs font-bold uppercase tracking-widest transition-all ${
                plan.highlight 
                  ? 'bg-primary text-white shadow-xl shadow-primary/20' 
                  : 'bg-surface-container-highest/60 text-on-surface hover:bg-surface-container-highest border border-white/10'
              }`}
            >
              Get Started
            </motion.button>
          </motion.div>
        ))}
      </div>

      <div className="mt-32 glass-panel p-12 rounded-[3.5rem] border-white/5 bg-gradient-to-br from-surface-container-low to-surface-container">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
          <div className="space-y-6">
            <h2 className="text-3xl font-semibold tracking-tight">Need a custom solution?</h2>
            <p className="text-on-surface-variant font-medium leading-relaxed">
              We offer volume discounts and custom deployments for large post-production houses and educational institutions.
            </p>
            <div className="flex gap-4">
              <div className="flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest">
                <Shield className="w-4 h-4" />
                Enterprise Security
              </div>
              <div className="flex items-center gap-2 text-secondary font-bold text-[10px] uppercase tracking-widest">
                <Crown className="w-4 h-4" />
                24/7 Support
              </div>
            </div>
          </div>
          <div className="flex justify-end">
            <button className="px-10 py-5 bg-white text-surface rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-on-surface-variant transition-all">
              Contact Sales
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
