import React, { useState } from 'react';

export default function PhotoGallery({ photos = [] }) {
  const [selectedPhoto, setSelectedPhoto] = useState(null);
  const [currentIndex, setCurrentIndex] = useState(0);

  if (!photos || photos.length === 0) {
    return null; // Don't render anything if no photos
  }

  const openLightbox = (photo, index) => {
    setSelectedPhoto(photo);
    setCurrentIndex(index);
  };

  const closeLightbox = () => {
    setSelectedPhoto(null);
  };

  const nextPhoto = () => {
    const newIndex = (currentIndex + 1) % photos.length;
    setCurrentIndex(newIndex);
    setSelectedPhoto(photos[newIndex]);
  };

  const prevPhoto = () => {
    const newIndex = currentIndex === 0 ? photos.length - 1 : currentIndex - 1;
    setCurrentIndex(newIndex);
    setSelectedPhoto(photos[newIndex]);
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextPhoto();
    if (e.key === 'ArrowLeft') prevPhoto();
  };

  return (
    <div className="photo-gallery">
      {/* Photo Grid */}
      <div className="photo-grid">
        {photos.map((photo, index) => (
          <div
            key={photo.id}
            className="photo-item"
            onClick={() => openLightbox(photo, index)}
          >
            <img
              src={photo.thumbnail}
              alt={photo.alt || photo.caption || `Preserve photo ${index + 1}`}
              loading="lazy"
            />
            {photo.caption && (
              <div className="photo-caption-overlay">
                {photo.caption}
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Lightbox */}
      {selectedPhoto && (
        <div 
          className="lightbox-overlay" 
          onClick={closeLightbox}
          onKeyDown={handleKeyDown}
          tabIndex={0}
        >
          <div className="lightbox-content" onClick={(e) => e.stopPropagation()}>
            {/* Close button */}
            <button className="lightbox-close" onClick={closeLightbox}>
              ✕
            </button>

            {/* Navigation buttons */}
            {photos.length > 1 && (
              <>
                <button className="lightbox-nav lightbox-prev" onClick={prevPhoto}>
                  ‹
                </button>
                <button className="lightbox-nav lightbox-next" onClick={nextPhoto}>
                  ›
                </button>
              </>
            )}

            {/* Main image */}
            <div className="lightbox-image-container">
              <img
                src={selectedPhoto.url}
                alt={selectedPhoto.alt || selectedPhoto.caption || 'Preserve photo'}
                className="lightbox-image"
              />
            </div>

            {/* Caption */}
            {selectedPhoto.caption && (
              <div className="lightbox-caption">
                {selectedPhoto.caption}
              </div>
            )}

            {/* Counter */}
            <div className="lightbox-counter">
              {currentIndex + 1} of {photos.length}
            </div>
          </div>
        </div>
      )}

      <style jsx>{`
        .photo-gallery {
          width: 100%;
        }

        .photo-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
          gap: 8px;
          margin-bottom: 16px;
        }

        .photo-item {
          position: relative;
          aspect-ratio: 1;
          border-radius: 8px;
          overflow: hidden;
          cursor: pointer;
          transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .photo-item:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .photo-item img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.2s ease;
        }

        .photo-item:hover img {
          transform: scale(1.05);
        }

        .photo-caption-overlay {
          position: absolute;
          bottom: 0;
          left: 0;
          right: 0;
          background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
          color: white;
          padding: 8px;
          font-size: 12px;
          opacity: 0;
          transition: opacity 0.2s ease;
        }

        .photo-item:hover .photo-caption-overlay {
          opacity: 1;
        }

        /* Lightbox Styles */
        .lightbox-overlay {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.9);
          display: flex;
          align-items: center;
          justify-content: center;
          z-index: 10000;
          animation: fadeIn 0.2s ease;
        }

        .lightbox-content {
          position: relative;
          max-width: 90vw;
          max-height: 90vh;
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .lightbox-close {
          position: absolute;
          top: -40px;
          right: -40px;
          background: rgba(255, 255, 255, 0.2);
          border: none;
          color: white;
          font-size: 24px;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          cursor: pointer;
          transition: background 0.2s ease;
          z-index: 10001;
        }

        .lightbox-close:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-nav {
          position: absolute;
          top: 50%;
          transform: translateY(-50%);
          background: rgba(255, 255, 255, 0.2);
          border: none;
          color: white;
          font-size: 32px;
          width: 50px;
          height: 50px;
          border-radius: 50%;
          cursor: pointer;
          transition: background 0.2s ease;
          z-index: 10001;
        }

        .lightbox-nav:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        .lightbox-prev {
          left: -60px;
        }

        .lightbox-next {
          right: -60px;
        }

        .lightbox-image-container {
          max-width: 100%;
          max-height: 80vh;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .lightbox-image {
          max-width: 100%;
          max-height: 100%;
          object-fit: contain;
          border-radius: 4px;
        }

        .lightbox-caption {
          margin-top: 16px;
          color: white;
          text-align: center;
          font-size: 16px;
          max-width: 600px;
          line-height: 1.4;
        }

        .lightbox-counter {
          position: absolute;
          bottom: -40px;
          left: 50%;
          transform: translateX(-50%);
          color: rgba(255, 255, 255, 0.8);
          font-size: 14px;
        }

        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
          .photo-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
          }

          .lightbox-close {
            top: 10px;
            right: 10px;
          }

          .lightbox-nav {
            display: none; /* Hide nav buttons on mobile, use swipe */
          }

          .lightbox-counter {
            bottom: 10px;
          }

          .lightbox-caption {
            font-size: 14px;
            margin-top: 12px;
            padding: 0 20px;
          }
        }

        /* Very small screens */
        @media (max-width: 480px) {
          .photo-grid {
            grid-template-columns: repeat(2, 1fr);
          }
        }
      `}</style>
    </div>
  );
}