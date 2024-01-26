# bella grande backend

## installation

When using Laravel Valet for serving the cms locally, run:

```bash
cd bella-grande/backend
valet link bella-grande
```

Now the backend is accessible on `http://bella-grande.test`

## helpers

### Download assets from staging:

`scp -r forge@164.92.193.139:~/bellagrande.fallwinter.dk/backend/web/media/ /Users/path/to/target-folder`

### Download assets from live:

`scp -r forge@164.92.193.139:~/cms.hotelbellagrande.com/backend/web/media/ /Users/path/to/target-folder`