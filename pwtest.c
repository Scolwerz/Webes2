#include <stdio.h>
#include <stdlib.h>
#include <string.h>

void decrypt(char *data, int *keys, int keys_length) {
    int data_length = strlen(data);
    int key_index = 0;

    for (int i = 0; i < data_length; i++) {
        if (data[i] != '\n') {
            printf("%c", data[i] -= keys[key_index % keys_length]);
        }
        key_index++;
        if (data[i] == '\n') {
            printf("%c", data[i]);
            key_index = 0;
        }
    }
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

    // Beolvassuk a fájl sorait, majd dekódoljuk őket
    while (fgets(line, sizeof(line), file)) {
        decrypt(line, keys, keys_length);
        printf("\n");
    }

    fclose(file);
    return 0;
}
