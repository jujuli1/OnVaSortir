import { test, expect } from '@playwright/test';
import { execSync } from 'child_process';


// charge la fixture
test.beforeAll(() => {
    execSync('docker compose exec php php bin/console doctrine:fixtures:load --env=test --no-interaction', { stdio: 'inherit' });
});

test('inscription nouvel utilisateur', async ({ page }) => {


    await page.goto('http://localhost:8080/register');


    await page.fill('#registration_form_nom', 'Julien');
    await page.fill('#registration_form_prenom', 'Lefevre');

    // email unique
    const email = `user${Date.now()}@playwright.test`;
    await page.fill('#registration_form_email', email);

    // Mot de passe
    await page.fill('#registration_form_motDePasse_first', 'Jujujujuvq2pq&');
    await page.fill('#registration_form_motDePasse_second', 'Jujujujuvq2pq&');


    await page.getByLabel('Campus').selectOption({ label: 'Campus principal' });


    await page.fill('#registration_form_birthday', '1991-12-22');

    // clique
    await page.getByRole('button', { name: "S'inscrire" }).click();

    // verifier la redirection vers login / profil / success
    // Aller sur la page de login
    await page.goto('http://localhost:8080/login');

    // Remplir le formulaire avec l'utilisateur créé via fixture
    await page.fill('#inputEmail', 'test@playwright.fr');
    await page.fill('#inputPassword', 'Jujujujuvq2pq&');

    await page.getByRole('button', { name: /connexion|se connecter/i }).click();

    await page.waitForLoadState('networkidle');

    // redirection vers le profil
    await expect(page).toHaveURL('http://localhost:8080/profil');
});




