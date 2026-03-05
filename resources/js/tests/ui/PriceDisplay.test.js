import { render, screen } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import PriceDisplay from "../../components/ui/PriceDisplay.svelte";

describe("PriceDisplay Component", () => {
    it("formats the price with default currency and suffix", () => {
        render(PriceDisplay, { price: 2500 });
        expect(screen.getByText("$2,500")).toBeTruthy();
        expect(screen.getByText("/mo")).toBeTruthy();
    });

    it("supports custom currency and suffix", () => {
        render(PriceDisplay, { price: 100, currency: "€", suffix: "/wk" });
        expect(screen.getByText("€100")).toBeTruthy();
        expect(screen.getByText("/wk")).toBeTruthy();
    });

    it("applies the correct size class", () => {
        const { container } = render(PriceDisplay, { price: 100, size: "xl" });
        const priceElement = screen.getByText("$100");
        expect(priceElement.className).toContain("text-5xl");
    });

    it("supports various size variants", () => {
        const { unmount } = render(PriceDisplay, { price: 100, size: "sm" });
        expect(screen.getByText("$100").className).toContain("text-lg");
        unmount();

        render(PriceDisplay, { price: 100, size: "lg" });
        expect(screen.getByText("$100").className).toContain("text-3xl");
    });
});
