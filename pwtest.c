#include <stdio.h>
#include <stdlib.h>
#include <string.h>

char* decrypt(char* data) {
    int keys[] = {5, -14, 31, -9, 3};
    int keys_length = 5;
    char* decrypted_data = (char*)malloc(strlen(data) + 1); if (decrypted_data == NULL) { fprintf(stderr, "Hiba: Nem sikerült memóriát foglalni!\n"); exit(1); }
    int key_index = 0;
    int data_length = strlen(data);
    for (int i = 0; i < data_length; i++) {
        if (data[i] != '\n') {
            int offset = keys[key_index % keys_length];
            char decoded_char = data[i] - offset;
            if (decoded_char < 32) {
                decrypted_data[i] = data[i];
            } else {
                decrypted_data[i] = decoded_char;
            }
            key_index++;
        }
        else {
            decrypted_data[i] = data[i];
            key_index = 0;
        }
    }
    decrypted_data[data_length] = '\0';
    return decrypted_data;
}

int main() {
    FILE *file = fopen("passwords.txt", "r");
    if (file == NULL) {
        perror("Unable to open file");
        return 1;
    }

    char line[222]; // Maximum sorhossz 100 karakter lehet
    int keys[] = {5, -14, 31, -9, 3};
    int keys_length = sizeof(keys) / sizeof(keys[0]);
    char text[2222];
    int i = 0;
    // Beolvassuk a fájl sorait, majd dekódoljuk őket
    while (fgets(line, sizeof(line), file)) {
        for (int c = 0; c < strlen(line); c++) {
            text[i++] = line[c];
        }
    }
    char* res = decrypt(text);
    printf("%s", res);
    free(res);
    fclose(file);
    return 0;
}
