import { render, screen, fireEvent } from "@testing-library/svelte";
import { describe, it, expect } from "vitest";
import MediaGallery from "../../components/ui/MediaGallery.svelte";

describe("MediaGallery Component", () => {
    const media = [
        { type: "property_image", path: "img1.jpg" },
        { type: "property_image", path: "img2.jpg" }
    ];

    it("renders fallback when no images", () => {
        render(MediaGallery, { media: [] });
        expect(screen.getByText("No images available")).toBeTruthy();
    });

    it("renders first image by default", () => {
        render(MediaGallery, { media });
        const img = screen.getByAltText("Property View");
        expect(img.src).toContain("img1.jpg");
    });

    it("switches image when next button clicked", async () => {
        render(MediaGallery, { media });
        const nextBtn = screen.getByLabelText("Next image");
        await fireEvent.click(nextBtn);
        
        const img = screen.getByAltText("Property View");
        expect(img.src).toContain("img2.jpg");
    });

    it("switches image when prev button clicked", async () => {
        render(MediaGallery, { media });
        const prevBtn = screen.getByLabelText("Previous image");
        await fireEvent.click(prevBtn);
        
        // Wrap around to the last image
        const img = screen.getByAltText("Property View");
        expect(img.src).toContain("img2.jpg");
    });

    it("switches image when dot indicator clicked", async () => {
        render(MediaGallery, { media });
        const dot2 = screen.getByLabelText("View image 2");
        await fireEvent.click(dot2);
        
        const img = screen.getByAltText("Property View");
        expect(img.src).toContain("img2.jpg");
    });

    it("applies custom class name", () => {
        const { container } = render(MediaGallery, { media: [], class: "custom-gallery" });
        expect(container.firstChild.className).toContain("custom-gallery");
    });
});
