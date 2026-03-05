import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import EmptyState from "../../components/ui/EmptyState.svelte";

describe("EmptyState Component", () => {
    it("renders title and message", () => {
        render(EmptyState, { 
            title: "No data", 
            message: "Try searching again" 
        });

        expect(screen.getByText("No data")).toBeTruthy();
        expect(screen.getByText("Try searching again")).toBeTruthy();
    });

    it("renders icon when provided as string", () => {
        render(EmptyState, { 
            icon: "search_off",
            title: "Empty",
            message: "Msg"
        });

        expect(screen.getByText("search_off")).toBeTruthy();
    });

    it("calls onAction when action button is clicked", async () => {
        const onAction = vi.fn();
        render(EmptyState, { 
            title: "Empty",
            message: "Msg",
            actionLabel: "Click Me",
            onAction
        });

        const button = screen.getByText("Click Me");
        await fireEvent.click(button);
        expect(onAction).toHaveBeenCalled();
    });
});
