<script setup>
const downloadPdf = async () => {
    //showing the PDF to tab
    window.open("/api/generate-pdf", "_blank");

    //Direct Downling the PDF
    // const response = await axios.get("/api/generate-pdf", {
    //     responseType: "blob",
    // });
    // const url = window.URL.createObjectURL(new Blob([response.data]));
    // const link = document.createElement("a");
    // link.href = url;
    // link.setAttribute("download", "attendance_report.pdf");
    // document.body.appendChild(link);
    // link.click();
};
</script>

<template>
    <div>
        <button @click="downloadPdf">Download</button>
    </div>
</template>
