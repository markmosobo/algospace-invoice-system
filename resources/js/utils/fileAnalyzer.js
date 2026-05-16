import * as pdfjsLib from "pdfjs-dist";
import { createWorker } from "tesseract.js";

// Required for PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc =
  "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";

/**
 * Analyze file and return estimated pages
 */
export async function analyzeFile(fileUrl, fileType) {

  // =========================
  // 1. PDF PAGE COUNT (ACCURATE)
  // =========================
  if (fileType === "pdf") {

    const loadingTask = pdfjsLib.getDocument(fileUrl);
    const pdf = await loadingTask.promise;

    return {
      pages: pdf.numPages,
      method: "pdf.js"
    };
  }

  // =========================
  // 2. IMAGE OCR (SMART ESTIMATE)
  // =========================
  if (["jpg", "jpeg", "png"].includes(fileType)) {

    const worker = await createWorker("eng");

    const { data } = await worker.recognize(fileUrl);

    await worker.terminate();

    // crude hybrid logic:
    // more text = likely document page
    const textLength = data.text.length;

    let pages = 1;

    if (textLength > 2000) pages = 2;
    if (textLength > 4000) pages = 3;

    return {
      pages,
      method: "tesseract.js",
      confidence: data.confidence
    };
  }

  // =========================
  // 3. DEFAULT FALLBACK
  // =========================
  return {
    pages: 1,
    method: "fallback"
  };
}