// External dependencies.
import Skeleton from 'react-loading-skeleton';

const CreateFontControlLoader = () => {
  return (
    <div>
      <div className="container-fluid p-0">
        <div className="row">
          <div className="col-12 mb-3">
            <Skeleton height={42} />
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

export default CreateFontControlLoader;
