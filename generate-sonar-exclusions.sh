#!/bin/bash

echo "Generando exclusiones para archivos raíz en la carpeta app/..."

exclusions=""
for file in app/*; do
  if [ -f "$file" ]; then
    exclusions+="$(echo "$file"),\\\n"
  fi
done

# Puedes copiar esto y pegarlo en tu sonar-project.properties
echo -e "\n# Exclusiones solo para archivos en la raíz de app/"
echo -e "sonar.exclusions=${exclusions}\
app/Console/**,\\
app/Events/**,\\
app/Exceptions/**,\\
app/Middleware/**,\\
app/Jobs/**,\\
app/Listeners/**,\\
app/Providers/**,\\
coverage-report/**,\\
database/**,\\
public/**,\\
storage/**,\\
tests/**,\\
upload/**"
