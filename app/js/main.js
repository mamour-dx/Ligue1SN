document.addEventListener('DOMContentLoaded', function() {
    // Filtrage des matchs
    const filterMatchs = () => {
        const equipe = document.getElementById('filter-equipe').value;
        const date = document.getElementById('filter-date').value;
        
        fetch(`/api/matchs.php?equipe=${equipe}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#matchs-table tbody');
                tableBody.innerHTML = '';
                
                data.forEach(match => {
                    const row = `
                        <tr>
                            <td>${match.date_heure}</td>
                            <td>${match.equipe_domicile}</td>
                            <td>${match.score_domicile !== null ? match.score_domicile : '-'}</td>
                            <td>${match.score_exterieur !== null ? match.score_exterieur : '-'}</td>
                            <td>${match.equipe_exterieur}</td>
                            <td>${match.lieu}</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            })
            .catch(error => console.error('Erreur:', error));
    };

    // Gestionnaire d'événements pour les filtres
    const filterEquipe = document.getElementById('filter-equipe');
    const filterDate = document.getElementById('filter-date');
    
    if (filterEquipe && filterDate) {
        filterEquipe.addEventListener('change', filterMatchs);
        filterDate.addEventListener('change', filterMatchs);
    }

    // Confirmation de suppression
    const confirmDelete = (event) => {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            event.preventDefault();
        }
    };

    // Ajouter la confirmation à tous les boutons de suppression
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', confirmDelete);
    });

    // Validation des formulaires
    const validateForm = (event) => {
        const form = event.target;
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        if (!isValid) {
            event.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
        }
    };

    // Ajouter la validation à tous les formulaires
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', validateForm);
    });
}); 