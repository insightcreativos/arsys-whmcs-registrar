<img alt="Arsys" src="https://raw.githubusercontent.com/insightcreativos/arsys-whmcs-registrar/master/logo.png?sanitize=true&raw=true" />

# WHMCS REGISTRAR
### Módulo arsys para añadir nuevo domain registrar a WHMCS


## Funcionalidad

- Registro, transferencia y renovación de dominios a Arsys
- Obtención de información de un dominio
- Cambio de nameservers
- Modificación y creación de nuevos contactos de dominios

## Requisitos
Este proyecto requiere de [la librería de integración con la API Redsys](https://github.com/insightcreativos/arsys-api-sdk-php)
- Esta librería se instala en ```<root_folder>/src/includes```


## Instalación

1. Descargar el proyecto y subirlo a ```<root_folder>/src/modules/registrars```
2. Activar el módulo desde Apps&Integrations
3. Una vez activo ir a ```System Settings``` -> ```Domain Registrars``` e incluir la GMD API Key de Arsys que encontrarás en tu cuenta GMD -> ```Configuración``` -> ```API```
4. Después de esto hay que establecer para qué TLDs es este nuevo registrador: para ello ir ```System Settings``` -> ```Domain pricing``` y activar "Arsys" para los TLDs que se quieran

Con esto, los usuarios podrán administrar sus dominios en WHMCS.

