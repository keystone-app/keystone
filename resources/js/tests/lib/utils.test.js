import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";
import { cn, debounce } from "../../lib/utils";

describe("utils.js", () => {
    describe("cn", () => {
        it("merges tailwind classes correctly", () => {
            expect(cn("px-2 py-2", "px-4")).toBe("py-2 px-4");
        });

        it("handles conditional classes", () => {
            expect(cn("base", true && "active", false && "hidden")).toBe("base active");
        });

        it("handles undefined and null", () => {
            expect(cn("base", undefined, null, "extra")).toBe("base extra");
        });
    });

    describe("debounce", () => {
        beforeEach(() => {
            vi.useFakeTimers();
        });

        afterEach(() => {
            vi.useRealTimers();
        });

        it("debounces function calls", () => {
            const fn = vi.fn();
            const debounced = debounce(fn, 100);

            debounced("a");
            debounced("b");
            debounced("c");

            expect(fn).not.toHaveBeenCalled();

            vi.advanceTimersByTime(100);

            expect(fn).toHaveBeenCalledTimes(1);
            expect(fn).toHaveBeenCalledWith("c");
        });

        it("calls the function immediately if wait is 0", () => {
            const fn = vi.fn();
            const debounced = debounce(fn, 0);

            debounced("test");
            
            // Even with 0, setTimeout is used, so we still need to advance
            vi.advanceTimersByTime(0);
            
            expect(fn).toHaveBeenCalledWith("test");
        });
    });
});
