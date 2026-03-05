import '@testing-library/jest-dom';

// Mock FileReader
global.FileReader = class {
    readAsDataURL() {
        setTimeout(() => {
            this.onload({ target: { result: "data:image/png;base64,test" } });
        }, 0);
    }
};

// Mock URL methods
global.URL.createObjectURL = (file) => "blob:test";
global.URL.revokeObjectURL = (url) => {};
