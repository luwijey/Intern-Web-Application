//download button for intern side
document.addEventListener("DOMContentLoaded", function () {

    // Select the download button
    const downloadButton2 = document.getElementById("downloadbtn");

    if (!downloadButton2) {
        console.error("❌ Download button not found!");
        return;
    }

    downloadButton2.addEventListener("click", function () {
    
        const tableWrapper1 = document.getElementById("historyTable");

        if (!tableWrapper1) {
            console.error("Table wrapper not found!");
            return;
        }

        html2canvas(tableWrapper1, { scale: 2 }).then((canvas) => {
            console.log("✅ Canvas created successfully");

            const imgData = canvas.toDataURL("image/png");
            const link = document.createElement("a");
            link.href = imgData;
            link.download = "Attendance_History.png";

            // Append link, trigger click, then remove
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log("✅ Download triggered");
        }).catch((err) => {
            console.error("❌ Error capturing canvas:", err);
        });
    });
});
