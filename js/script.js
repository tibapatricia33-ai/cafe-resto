function afficherDate() {

    const aujourdhui = new Date();

    const options = {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    };

    document.getElementById("date").innerHTML =
        aujourdhui.toLocaleDateString('fr-FR', options);

}

afficherDate();


const boutonsSupprimer = document.querySelectorAll(".btn-supprimer");

boutonsSupprimer.forEach(function(bouton) {

    bouton.addEventListener("click", function() {

        let confirmation = confirm("Voulez-vous vraiment supprimer ce produit ?");

        if (confirmation) {

            this.closest("tr").remove();

        }

    });

});
function afficherDate() {

    const dateElement = document.getElementById("date");

    if (dateElement) {

        const aujourdHui = new Date();

        const options = {
            weekday: "long",
            day: "numeric",
            month: "long",
            year: "numeric"
        };

        dateElement.textContent =
            aujourdHui.toLocaleDateString("fr-FR", options);
    }
}

afficherDate();