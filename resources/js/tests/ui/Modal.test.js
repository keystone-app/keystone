import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect, vi } from "vitest";
import Modal from "../../components/ui/Modal.svelte";
import { createRawSnippet } from "svelte";

describe("Modal Component", () => {
    it("renders children when open", () => {
        const onClose = vi.fn();
        const children = createRawSnippet(() => ({
            render: () => "<div>Modal Content</div>",
        }));

        render(Modal, {
            isOpen: true,
            onClose,
            children,
            title: "Test Modal"
        });

        expect(screen.getByText("Test Modal")).toBeTruthy();
        expect(screen.getByText("Modal Content")).toBeTruthy();
    });

    it("does not render content when closed", () => {
        const onClose = vi.fn();
        const children = createRawSnippet(() => ({
            render: () => "<div>Modal Content</div>",
        }));

        render(Modal, {
            isOpen: false,
            onClose,
            children,
            title: "Test Modal"
        });

        expect(screen.queryByText("Test Modal")).toBeNull();
        expect(screen.queryByText("Modal Content")).toBeNull();
    });

    // Note: bits-ui Dialog.Close usually handles the click, 
    // but we can test the onClose callback via onOpenChange if we simulate it properly.
    // However, testing third-party component internal behavior is often brittle.
});
