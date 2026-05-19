import { Hero } from "../components/Hero";
import { EditingEngine } from "../components/EditingEngine";
import { Features } from "../components/Features";
import { Workspace } from "../components/Workspace";
import { Interoperability } from "../components/Interoperability";

export function Home() {
  return (
    <>
      <Hero />
      <EditingEngine />
      <Features />
      <Workspace />
      <Interoperability />
    </>
  );
}
