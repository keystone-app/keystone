import { render, screen } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import Card from "../../components/ui/Card.svelte";
import { createRawSnippet } from "svelte";

describe("Card Component", () => {
    it("renders children content", () => {
        const snippet = createRawSnippet(() => ({
            render: () => '<div>Body Content</div>',
        }));

        render(Card, { children: snippet });
        expect(screen.getByText("Body Content")).toBeTruthy();
    });

    it("renders header and footer when provided", () => {
        const header = createRawSnippet(() => ({
            render: () => '<div>Header</div>',
        }));
        const footer = createRawSnippet(() => ({
            render: () => '<div>Footer</div>',
        }));
        const children = createRawSnippet(() => ({
            render: () => '<div>Body</div>',
        }));

        render(Card, { header, footer, children });
        
        expect(screen.getByText("Header")).toBeTruthy();
        expect(screen.getByText("Footer")).toBeTruthy();
    });

    it("applies hover classes by default", () => {
        const children = createRawSnippet(() => ({ render: () => '<div>Body</div>' }));
        const { container } = render(Card, { children });
        const card = container.firstChild;
        expect(card.className).toContain("hover:shadow-xl");
    });
});
