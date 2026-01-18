const { Storage } = require('@google-cloud/storage');
const path = require('path');

let storage = null;

const getStorage = () => {
  if (!storage) {
    // Untuk akses lokal, uncomment kode di bawah ini dan set GOOGLE_APPLICATION_CREDENTIALS ke path file key JSON
    /*
    if (process.env.GOOGLE_APPLICATION_CREDENTIALS) {
      storage = new Storage({
        projectId: process.env.GCP_PROJECT_ID,
        keyFilename: process.env.GOOGLE_APPLICATION_CREDENTIALS
      });
    } else 
    */
    if (process.env.GCS_CREDENTIALS) {
      // Untuk environment variable JSON
      storage = new Storage({
        projectId: process.env.GCP_PROJECT_ID,
        credentials: JSON.parse(process.env.GCS_CREDENTIALS)
      });
    } else {
      // Default: gunakan credential dari environment Cloud Run
      storage = new Storage();
    }
  }
  return storage;
};

const uploadFileToGCS = async (bucket, destination, filePath, mimeType = 'application/octet-stream') => {
  try {
    const storage = getStorage();
    const gcs = storage.bucket(bucket);
    
    await gcs.upload(filePath, {
      destination: destination,
      metadata: {
        contentType: mimeType,
        cacheControl: 'public, max-age=604800' // 7 days cache
      }
    });

    // Return public URL
    return `https://storage.googleapis.com/${bucket}/${destination}`;
  } catch (error) {
    console.error('Error uploading to GCS:', error);
    throw error;
  }
};

const deleteFileFromGCS = async (bucket, destination) => {
  try {
    const storage = getStorage();
    const gcs = storage.bucket(bucket);
    await gcs.file(destination).delete();
    console.log(`Deleted ${destination} from GCS`);
  } catch (error) {
    console.error('Error deleting from GCS:', error);
    // Don't throw - continue if file doesn't exist
  }
};

const getFilePathFromUrl = (url) => {
  // Extract destination path from GCS URL
  // Format: https://storage.googleapis.com/bucket-name/path/to/file
  const match = url.match(/https:\/\/storage\.googleapis\.com\/[^/]+\/(.+)/);
  return match ? match[1] : null;
};

const getSignedUrl = async (bucket, destination, expiresIn = 3600) => {
  try {
    const storage = getStorage();
    const gcs = storage.bucket(bucket);
    const file = gcs.file(destination);
    
    const [url] = await file.getSignedUrl({
      version: 'v4',
      action: 'read',
      expires: Date.now() + expiresIn * 1000, // convert to milliseconds
    });
    
    return url;
  } catch (error) {
    console.error('Error generating signed URL:', error);
    throw error;
  }
};

module.exports = {
  getStorage,
  uploadFileToGCS,
  deleteFileFromGCS,
  getFilePathFromUrl,
  getSignedUrl
};
