// External dependencies.
import Skeleton from 'react-loading-skeleton';

const EditFontControlLoader = () => {
  return (
    <div>
      <div className="container-fluid p-0">
        <div className="row">
          <div className="col-12 mb-3">
            <Skeleton height={70} />
          </div>

          {/* Settings placeholder */}
          <div className="col">
            <Skeleton height={68} style={{ marginBottom: 1 }} />
            <Skeleton height={420} style={{ marginBottom: 1 }} />
            <Skeleton height={68} style={{ marginBottom: 0 }} />
          </div>
        </div>
      </div>
    </div>
  );
};

export default EditFontControlLoader;
